<?php namespace App\Http;

use App\Json\Controllers\BoardsController as ApiBoardsController;
use App\Json\Controllers\CommentsController as ApiCommentsController;
use App\Json\Controllers\PostsController as ApiPostsController;
use App\Json\Controllers\RolesController as ApiRolesController;
use App\Json\Controllers\UsersController as ApiUsersController;
use App\Json\Schemes\BoardScheme;
use App\Json\Schemes\PostScheme;
use App\Json\Schemes\UserScheme;
use App\Web\Controllers\BoardsController as WebBoardsController;
use App\Web\Controllers\PostsController as WebPostsController;
use Limoncello\Application\Commands\DataCommand;
use Limoncello\Application\Packages\Application\WhoopsContainerConfigurator;
use Limoncello\Commands\CommandRoutesTrait;
use Limoncello\Contracts\Application\RoutesConfiguratorInterface;
use Limoncello\Contracts\Routing\GroupInterface as GI;
use Limoncello\Flute\Contracts\Http\ControllerInterface;
use Limoncello\Flute\Http\Traits\FluteRoutesTrait;
use Limoncello\Flute\Package\FluteContainerConfigurator;
use Settings\Commands;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Routes implements RoutesConfiguratorInterface
{
    use FluteRoutesTrait, CommandRoutesTrait;

    /** API URI prefix */
    const API_URI_PREFIX = '/api/v1';

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

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function configureRoutes(GI $routes): void
    {
        // Every group, controller and even method may have custom `Request` factory and `Container` configurator.
        // Thus container for `API` and `Web` groups can be configured differently which could be used for
        // improving page load time for every HTTP route.
        // Container can be configured even for individual controller method (e.g. `PaymentsController::index`).
        // Also custom middleware could be specified for a group, controller or method.

        $routes
            // HTML pages group
            // This group uses exception handler to provide error information in HTML format with Whoops.
            ->group('', function (GI $routes): void {

                $slugged = '/{' . ControllerInterface::ROUTE_KEY_INDEX . '}/';
                $routes->get('/', [WebBoardsController::class, WebBoardsController::METHOD_INDEX]);
                $routes->get('/boards' . $slugged, [WebBoardsController::class, WebBoardsController::METHOD_READ]);
                self::controller($routes, '/posts', WebPostsController::class);

            }, [
                GI::PARAM_CONTAINER_CONFIGURATORS => [
                    WhoopsContainerConfigurator::CONFIGURE_EXCEPTION_HANDLER,
                ],
            ])
            // JSON API group
            // This group uses custom exception handler to provide error information in JSON API format.
            ->group(self::API_URI_PREFIX, function (GI $routes): void {

                self::resource($routes, ApiBoardsController::class);
                self::relationship($routes, BoardScheme::REL_POSTS, ApiBoardsController::class, 'readPosts');

                self::resource($routes, ApiPostsController::class);
                self::relationship($routes, PostScheme::REL_COMMENTS, ApiPostsController::class, 'readComments');

                self::resource($routes, ApiCommentsController::class);

                self::resource($routes, ApiUsersController::class);
                self::relationship($routes, UserScheme::REL_POSTS, ApiUsersController::class, 'readPosts');
                self::relationship($routes, UserScheme::REL_COMMENTS, ApiUsersController::class, 'readComments');

                self::resource($routes, ApiRolesController::class);
            }, [
                GI::PARAM_CONTAINER_CONFIGURATORS => [
                    FluteContainerConfigurator::CONFIGURE_EXCEPTION_HANDLER,
                ],
            ]);

        // Console commands can have their custom containers too!
        // Configure container for limoncello `db` command so we can use data `Faker` for data seeding.
        self::commandContainer($routes, DataCommand::NAME, Commands::CONFIGURATOR);
    }
}
