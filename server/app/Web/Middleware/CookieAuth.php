<?php namespace App\Web\Middleware;

use Closure;
use Limoncello\Contracts\Application\MiddlewareInterface;
use Limoncello\Contracts\Passport\PassportAccountManagerInterface;
use Limoncello\Passport\Exceptions\AuthenticationException;
use Limoncello\Passport\Exceptions\RepositoryException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * @package App
 */
final class CookieAuth implements MiddlewareInterface
{
    const COOKIE_NAME = 'auth_token';

    /**
     * Middleware handler.
     */
    const CALLABLE_HANDLER = [self::class, self::MIDDLEWARE_METHOD_NAME];

    /**
     * @inheritdoc
     */
    public static function handle(
        ServerRequestInterface $request,
        Closure $next,
        ContainerInterface $container
    ): ResponseInterface {
        // if auth cookie given ...
        $cookies = $request->getCookieParams();
        if (array_key_exists(static::COOKIE_NAME, $cookies) === true &&
            is_string($tokenValue = $cookies[static::COOKIE_NAME]) === true &&
            empty($tokenValue) === false
        ) {
            // ... and user hasn't been authenticated before ...
            /** @var PassportAccountManagerInterface $accountManager */
            $accountManager = $container->get(PassportAccountManagerInterface::class);
            if ($accountManager->getAccount() === null) {
                // ... then auth with the cookie
                try {
                    $accountManager->setAccountWithTokenValue($tokenValue);
                } catch (AuthenticationException $exception) {
                    // ignore if auth with the token fails or add the accident to log (could be taken from container)
                    /** @var LoggerInterface $logger */
                    $logger = $container->get(LoggerInterface::class);
                    $logger->warning(
                        'Auth cookie received with request however authentication failed due to its invalid value.',
                        ['exception' => $exception]
                    );
                } catch (RepositoryException $exception) {
                    // ignore if auth with the token fails or add the accident to log (could be taken from container)
                    /** @var LoggerInterface $logger */
                    $logger = $container->get(LoggerInterface::class);
                    $logger->warning(
                        'Auth cookie received with request however authentication failed due to database issue(s).',
                        ['exception' => $exception]
                    );
                }
            }
        }

        return $next($request);
    }
}
