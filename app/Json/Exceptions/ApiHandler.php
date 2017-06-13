<?php namespace App\Json\Exceptions;

use Closure;
use Limoncello\Application\Exceptions\AuthorizationException;
use Limoncello\Contracts\Application\MiddlewareInterface;
use Limoncello\Passport\Exceptions\AuthenticationException;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 */
class ApiHandler implements MiddlewareInterface
{
    /** @var callable */
    const HANDLER = [self::class, self::MIDDLEWARE_METHOD_NAME];

    /**
     * @inheritdoc
     *
     * This code provides an ability to transform various exceptions in API (application specific,
     * authorization, 3rd party, etc) and convert it to JSON API error.
     *
     * In order to do that you will catch exception type you need and rethrow it as `JsonApiException`.
     */
    public static function handle(
        ServerRequestInterface $request,
        Closure $next,
        ContainerInterface $container
    ): ResponseInterface {
        try {
            return $next($request);
        } catch (AuthorizationException $exception) {
            $httpCode = 403;
            $action   = $exception->getAction();
            $errors   = (new ErrorCollection())
                ->addDataError(
                    'Unauthorized',
                    "You are not unauthorized for action `$action`.",
                    null,
                    null,
                    null,
                    $httpCode
                );
            throw new JsonApiException($errors, $httpCode, $exception);
        } catch (AuthenticationException $exception) {
            $httpCode = 401;
            $errors   = (new ErrorCollection())
                ->addDataError(
                    'Authentication failed',
                    'Authentication failed',
                    null,
                    null,
                    null,
                    $httpCode
                );
            throw new JsonApiException($errors, $httpCode, $exception);
        }
    }
}
