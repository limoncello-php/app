<?php namespace App\Routes;

use App\Commands\Middleware\CliAuthenticationMiddleware;
use App\Container\CliCommandsConfigurator;
use Limoncello\Application\Commands\DataCommand;
use Limoncello\Contracts\Commands\RoutesConfiguratorInterface;
use Limoncello\Contracts\Commands\RoutesInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CliRoutes implements RoutesConfiguratorInterface
{
    /**
     * @inheritdoc
     */
    public static function configureRoutes(RoutesInterface $routes): void
    {
        $routes
            ->addGlobalContainerConfigurators([
                CliCommandsConfigurator::CONFIGURATOR,
                ])
            ->addCommandMiddleware(DataCommand::NAME, [CliAuthenticationMiddleware::CALLABLE_HANDLER]);
    }
}
