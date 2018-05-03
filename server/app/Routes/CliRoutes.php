<?php namespace App\Routes;

use Limoncello\Application\Commands\ApplicationCommand;
use Limoncello\Application\Commands\DataCommand;
use Limoncello\Application\Packages\Application\ApplicationContainerConfigurator;
use Limoncello\Application\Packages\Data\DataContainerConfigurator;
use Limoncello\Application\Packages\FileSystem\FileSystemContainerConfigurator;
use Limoncello\Application\Packages\L10n\L10nContainerConfigurator;
use Limoncello\Application\Packages\Monolog\MonologFileContainerConfigurator;
use Limoncello\Commands\CommandRoutesTrait;
use Limoncello\Commands\CommandsCommand;
use Limoncello\Contracts\Application\RoutesConfiguratorInterface;
use Limoncello\Contracts\Routing\GroupInterface;
use Limoncello\Crypt\Package\HasherContainerConfigurator;
use Limoncello\Passport\Package\PassportContainerConfigurator;
use Limoncello\Templates\Commands\TemplatesCommand;
use Limoncello\Templates\Package\TwigTemplatesContainerConfigurator;
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
        // Individual console commands can have their custom containers too!
        // For example, limoncello `db` command might need `Faker` for data seeding.

        // commands require composer
        if (class_exists('Composer\Command\BaseCommand') === true) {
            // Common configurators that typically needed in commands.
            // We configure them independently from the main application so even if all
            // providers will be disabled in the main app the commands will continue to work.
            $commonConfigurators = [
                Commands::CONFIGURATOR,
                ApplicationContainerConfigurator::CONFIGURATOR,
                DataContainerConfigurator::CONFIGURATOR,
                L10nContainerConfigurator::CONFIGURATOR,
                MonologFileContainerConfigurator::CONFIGURATOR,
                FileSystemContainerConfigurator::CONFIGURATOR,
                HasherContainerConfigurator::CONFIGURATOR,
                PassportContainerConfigurator::CONFIGURATOR,
                TwigTemplatesContainerConfigurator::CONFIGURATOR,
            ];

            self::commandContainer($routes, DataCommand::NAME, $commonConfigurators);
            self::commandContainer($routes, ApplicationCommand::NAME, $commonConfigurators);
            self::commandContainer($routes, CommandsCommand::NAME, $commonConfigurators);
            self::commandContainer($routes, TemplatesCommand::NAME, $commonConfigurators);
        }
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
