<?php namespace App\Authorization\Middleware;

use App\Authentication\Contracts\AccountManagerInterface;
use Closure;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

/**
 * @package App
 */
class AuthenticationRequired
{
    /**
     * @param ServerRequestInterface $request
     * @param Closure                $next
     * @param ContainerInterface     $container
     *
     * @return ResponseInterface
     */
    public static function handle(ServerRequestInterface $request, Closure $next, ContainerInterface $container)
    {
        /** @var AccountManagerInterface $accountManager */
        $accountManager = $container->get(AccountManagerInterface::class);
        if ($accountManager->getAccount()->isAnonymous() === true) {
            return new EmptyResponse(401);
        }

        // call next middleware handler
        return $next($request);
    }
}
