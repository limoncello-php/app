<?php namespace App\Http;

use App\Http\Controllers\BoardsController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Json\Exceptions\ApiHandler;
use App\Json\Schemes\BoardScheme;
use App\Json\Schemes\PostScheme;
use App\Json\Schemes\UserScheme;
use Limoncello\Application\Commands\DataCommand;
use Limoncello\Commands\CommandRoutesTrait;
use Limoncello\Contracts\Application\RoutesConfiguratorInterface;
use Limoncello\Contracts\Routing\GroupInterface as GI;
use Limoncello\Contracts\Routing\RouteInterface as RI;
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
        // So you can have your own `CommentRequest` which would be created for `CommentsController` only.
        // Or you may configure container to have `PaymentGageInterface` available only in `PaymentsController`.
        // Also custom middleware could be specified for group, controller or method.

        $routes

            // This handler is very simple and we don't need `ServerRequestInterface`
            // so we can instruct not to create it for us.
            ->get('/', HomeController::INDEX_HANDLER, [
                RI::PARAM_REQUEST_FACTORY => null,
            ])

            // Container could be configured individually for each handler.
            // It helps to keep containers smaller and the application faster.
            ->get('/welcome', HomeController::WELCOME_HANDLER, [
                RI::PARAM_CONTAINER_CONFIGURATORS => [HomeController::CONTAINER_EXTRA_CONFIGURATOR],
            ])

            // JSON API group
            // This group uses custom container configurator and exception handler to
            // provide error information in JSON API format.
            ->group(self::API_URI_PREFIX, function (GI $routes) {

                self::resource($routes, BoardsController::class);
                self::relationship($routes, BoardScheme::REL_POSTS, BoardsController::class, 'readPosts');

                self::resource($routes, PostsController::class);
                self::relationship($routes, PostScheme::REL_COMMENTS, PostsController::class, 'readComments');

                self::resource($routes, CommentsController::class);

                self::resource($routes, UsersController::class);
                self::relationship($routes, UserScheme::REL_POSTS, UsersController::class, 'readPosts');
                self::relationship($routes, UserScheme::REL_COMMENTS, UsersController::class, 'readComments');

                self::resource($routes, RolesController::class);
            }, [
                GI::PARAM_CONTAINER_CONFIGURATORS => [FluteContainerConfigurator::CONFIGURE_EXCEPTION_HANDLER],
                GI::PARAM_MIDDLEWARE_LIST         => [ApiHandler::HANDLER],
            ])
        ;

        // Groups and subgroups are also supported.
        // Custom middleware, configurators and request factories are supported on
        // all levels from group to individual method.
        //
        //->group('api/v1', function (GI $group) {
        //    $group
        //        ->get($uriPath, $controllerClass . '::index')
        //        ->post($uriPath, $controllerClass . '::create')
        //        ->get($uriPath . '/{idx}', $controllerClass . '::read')
        //        ->patch($uriPath . '/{idx}', $controllerClass . '::update')
        //        ->delete($uriPath . '/{idx}', $controllerClass . '::delete');
        //}, [
        //      // Custom middleware for routes in the group
        //      GI::PARAM_MIDDLEWARE_LIST         => [ callable[] ],
        //      GI::PARAM_NAME_PREFIX             => 'custom/uri/prefix/for/all/routs',
        //      GI::PARAM_CONTAINER_CONFIGURATORS =>  [ callable[] ],
        //
        //      // Default `ServerRequestInterface` factory could replaced with custom so
        //      // you can have your own `AppOrRouteSpecificRequest`. If your app do not
        //      // use $request you may set `null` and request won't be created then. It
        //      // might give you a small performance improvement.
        //      GI::PARAM_REQUEST_FACTORY => callable|null,
        //]);

        // Configure container for limoncello `db` command so we can use data `Faker` for data seeding.
        self::commandContainer($routes, DataCommand::NAME, Commands::CONFIGURATOR);
    }
}
