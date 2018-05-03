<?php namespace App\Routes;

use App\Json\Controllers\RolesController;
use App\Json\Controllers\UsersController;
use App\Json\Schemas\RoleSchema;
use App\Json\Schemas\UserSchema;
use Limoncello\Commands\CommandRoutesTrait;
use Limoncello\Contracts\Application\RoutesConfiguratorInterface;
use Limoncello\Contracts\Routing\GroupInterface;
use Limoncello\Flute\Http\Traits\FluteRoutesTrait;
use Limoncello\Flute\Package\FluteContainerConfigurator;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApiRoutes implements RoutesConfiguratorInterface
{
    use FluteRoutesTrait, CommandRoutesTrait;

    /** API URI prefix */
    const API_URI_PREFIX = '/api/v1';

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
            // JSON API group
            // This group uses custom exception handler to provide error information in JSON API format.
            ->group(self::API_URI_PREFIX, function (GroupInterface $routes): void {

                $routes->addContainerConfigurators([
                    FluteContainerConfigurator::CONFIGURE_EXCEPTION_HANDLER,
                ]);

                self::apiController($routes, UserSchema::TYPE, UsersController::class);

                self::apiController($routes, RoleSchema::TYPE, RolesController::class);
                self::relationship($routes, RoleSchema::TYPE, RoleSchema::REL_USERS, RolesController::class, 'readUsers');
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
            //ClassName::class,
        ];
    }
}
