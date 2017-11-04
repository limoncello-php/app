<?php namespace App\Routes;

use App\Web\Controllers\BoardsController;
use App\Web\Controllers\PostsController;
use Limoncello\Application\Packages\Application\WhoopsContainerConfigurator;
use Limoncello\Commands\CommandRoutesTrait;
use Limoncello\Contracts\Application\RoutesConfiguratorInterface;
use Limoncello\Contracts\Routing\GroupInterface;
use Limoncello\Flute\Contracts\Http\ControllerInterface;
use Limoncello\Flute\Http\Traits\FluteRoutesTrait;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WebRoutes implements RoutesConfiguratorInterface
{
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
            ->group('', function (GroupInterface $routes): void {

                $slugged = '/{' . ControllerInterface::ROUTE_KEY_INDEX . '}/';

                $routes->get('/', [BoardsController::class, BoardsController::METHOD_INDEX]);
                $routes->get('/boards' . $slugged, [BoardsController::class, BoardsController::METHOD_READ]);

                self::controller($routes, '/posts', PostsController::class);

            }, [
                GroupInterface::PARAM_CONTAINER_CONFIGURATORS => [
                    WhoopsContainerConfigurator::CONFIGURE_EXCEPTION_HANDLER,
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
