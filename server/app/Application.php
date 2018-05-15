<?php namespace App;

use App\Container\TwigConfigurator;
use Limoncello\Contracts\Container\ContainerInterface as LimoncelloContainerInterface;
use Limoncello\Contracts\Core\SapiInterface;
use Settings\Application as ApplicationSettings;

/**
 * @package App
 */
class Application extends \Limoncello\Application\Packages\Application\Application
{
    /**
     * @inheritdoc
     */
    public function __construct(SapiInterface $sapi = null)
    {
        $settings =
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . '*.php';

        parent::__construct($settings, ApplicationSettings::CACHE_CALLABLE, $sapi);
    }

    /**
     * @inheritdoc
     */
    protected function configureContainer(
        LimoncelloContainerInterface $container,
        array $globalConfigurators = null,
        array $routeConfigurators = null
    ): void {
        parent::configureContainer($container, $globalConfigurators, $routeConfigurators);

        // You can override default container services as below
        TwigConfigurator::configureContainer($container);
    }
}
