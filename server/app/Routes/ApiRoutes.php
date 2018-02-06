<?php namespace App\Routes;

use App\Json\Controllers\BoardsController;
use App\Json\Controllers\CommentsController;
use App\Json\Controllers\PostsController;
use App\Json\Controllers\RolesController;
use App\Json\Controllers\UsersController;
use App\Json\Schemes\BoardSchema;
use App\Json\Schemes\PostSchema;
use App\Json\Schemes\UserSchema;
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

                self::resource($routes, BoardsController::class);
                self::relationship($routes, BoardSchema::REL_POSTS, BoardsController::class, 'readPosts');

                self::resource($routes, PostsController::class);
                self::relationship($routes, PostSchema::REL_COMMENTS, PostsController::class, 'readComments');

                self::resource($routes, CommentsController::class);

                self::resource($routes, UsersController::class);
                self::relationship($routes, UserSchema::REL_POSTS, UsersController::class, 'readPosts');
                self::relationship($routes, UserSchema::REL_COMMENTS, UsersController::class, 'readComments');

                self::resource($routes, RolesController::class);
            }, [
                GroupInterface::PARAM_CONTAINER_CONFIGURATORS => [
                    FluteContainerConfigurator::CONFIGURE_EXCEPTION_HANDLER,
                ],
            ]);
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
