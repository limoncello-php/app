<?php namespace App\Routes;

use App\Container\RequestStorageConfigurator;
use App\Web\Controllers\AuthController;
use App\Web\Controllers\HomeController;
use App\Web\Controllers\RolesController;
use App\Web\Controllers\UsersController;
use App\Web\Middleware\CookieAuth;
use App\Web\Middleware\CustomErrorResponsesMiddleware;
use App\Web\Middleware\RememberRequestMiddleware;
use Limoncello\Application\Packages\Application\WhoopsContainerConfigurator;
use Limoncello\Application\Packages\Csrf\CsrfContainerConfigurator;
use Limoncello\Application\Packages\Csrf\CsrfMiddleware;
use Limoncello\Application\Packages\Session\SessionContainerConfigurator;
use Limoncello\Application\Packages\Session\SessionMiddleware;
use Limoncello\Commands\CommandRoutesTrait;
use Limoncello\Contracts\Application\RoutesConfiguratorInterface;
use Limoncello\Contracts\Routing\GroupInterface;
use Limoncello\Contracts\Routing\RouteInterface;
use Limoncello\Flute\Contracts\Http\WebControllerInterface;
use Limoncello\Flute\Http\Traits\FluteRoutesTrait;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WebRoutes implements RoutesConfiguratorInterface
{
    const TOP_GROUP_PREFIX = '';

    use FluteRoutesTrait, CommandRoutesTrait;

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function configureRoutes(GroupInterface $routes): void
    {
        // Every group, controller and even method may have custom `Request` factory and `Container` configurator.
        // Thus container for `API` and `Web` groups can be configured differently which could be used for
        // improving page load time for every HTTP route.
        // Container can be configured even for individual controller method (e.g. `PaymentsController::index`).
        // Also custom middleware could be specified for a group, controller or method.

        $routes
            // HTML pages group
            // This group uses exception handler to provide error information in HTML format with Whoops.
            ->group(self::TOP_GROUP_PREFIX, function (GroupInterface $routes): void {

                $routes->addContainerConfigurators([
                    WhoopsContainerConfigurator::CONFIGURE_EXCEPTION_HANDLER,
                    CsrfContainerConfigurator::CONFIGURATOR,
                    SessionContainerConfigurator::CONFIGURATOR,
                    RequestStorageConfigurator::CONFIGURATOR,
                ])->addMiddleware([
                    CustomErrorResponsesMiddleware::CALLABLE_HANDLER,
                    SessionMiddleware::CALLABLE_HANDLER,
                    CsrfMiddleware::CALLABLE_HANDLER,
                    RememberRequestMiddleware::CALLABLE_HANDLER,
                ]);

                $routes
                    ->get('/', [HomeController::class, 'index'], [RouteInterface::PARAM_NAME => HomeController::ROUTE_NAME_HOME])
                    ->get('/sign-in', AuthController::CALLABLE_SHOW_SIGN_IN, [RouteInterface::PARAM_NAME => AuthController::ROUTE_NAME_SIGN_IN])
                    ->post('/sign-in', AuthController::CALLABLE_AUTHENTICATE)
                    ->get('/sign-out', AuthController::CALLABLE_LOGOUT, [RouteInterface::PARAM_NAME => AuthController::ROUTE_NAME_LOGOUT]);


                $idx = '{' . WebControllerInterface::ROUTE_KEY_INDEX . '}';

                self::webController($routes, 'users', UsersController::class);
                self::webController($routes, 'roles', RolesController::class);
                $routes->get("roles/$idx/users", RolesController::CALLABLE_READ_USERS, [RouteInterface::PARAM_NAME => RolesController::ROUTE_NAME_READ_USERS]);

            });
    }

    /**
     * This middleware will be executed on every request even when no matching route is found.
     *
     * @return string[]
     */
    public static function getMiddleware(): array
    {
        return [
            CookieAuth::class,
        ];
    }
}
