<?php namespace App\Container;

use Faker\Factory;
use Faker\Generator;
use Limoncello\Contracts\Commands\ContainerConfiguratorInterface;
use Limoncello\Contracts\Container\ContainerInterface as LimoncelloContainerInterface;

/**
 * @package Settings
 */
class CliCommandsConfigurator implements ContainerConfiguratorInterface
{
    /** @var callable */
    const CONFIGURATOR = [self::class, self::CONTAINER_METHOD_NAME];

    /**
     * @inheritdoc
     */
    public static function configureContainer(LimoncelloContainerInterface $container): void
    {
        $container[Generator::class] = function () {
            return Factory::create(Factory::DEFAULT_LOCALE);
        };
    }
}
