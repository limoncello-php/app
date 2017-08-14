<?php namespace Settings;

use Faker\Factory;
use Faker\Generator;
use Limoncello\Contracts\Application\ContainerConfiguratorInterface;
use Limoncello\Contracts\Container\ContainerInterface as LimoncelloContainerInterface;

/**
 * @package Settings
 */
class Commands implements ContainerConfiguratorInterface
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
