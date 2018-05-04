<?php namespace App\Container;

use Limoncello\Application\Http\RequestStorage;
use Limoncello\Contracts\Application\ContainerConfiguratorInterface;
use Limoncello\Contracts\Container\ContainerInterface as LimoncelloContainerInterface;
use Limoncello\Contracts\Http\RequestStorageInterface;

/**
 * @package App\Container
 */
class RequestStorageConfigurator implements ContainerConfiguratorInterface
{
    /** @var callable */
    const CONFIGURATOR = [self::class, self::CONTAINER_METHOD_NAME];

    /**
     * @inheritdoc
     */
    public static function configureContainer(LimoncelloContainerInterface $container): void
    {
        $container[RequestStorageInterface::class] = function () {
            return new RequestStorage();
        };
    }
}
