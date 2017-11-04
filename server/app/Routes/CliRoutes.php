<?php namespace App\Routes;

use Limoncello\Application\Commands\DataCommand;
use Limoncello\Commands\CommandRoutesTrait;
use Limoncello\Contracts\Application\RoutesConfiguratorInterface;
use Limoncello\Contracts\Routing\GroupInterface;
use Settings\Commands;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CliRoutes implements RoutesConfiguratorInterface
{
    use CommandRoutesTrait;

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function configureRoutes(GroupInterface $routes): void
    {
        // Console commands can have their custom containers too!
        // Configure container for limoncello `db` command so we can use data `Faker` for data seeding.
        self::commandContainer($routes, DataCommand::NAME, Commands::CONFIGURATOR);
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
