<?php namespace App\Container;

use Config\Application;
use Interop\Container\ContainerInterface;
use Limoncello\Core\Contracts\Config\ConfigInterface;
use Limoncello\JsonApi\Contracts\Http\Cors\CorsStorageInterface;
use Limoncello\JsonApi\Http\Cors\CorsStorage;
use Neomerx\Cors\Analyzer;
use Neomerx\Cors\Contracts\AnalyzerInterface;
use Neomerx\Cors\Strategies\Settings;
use Limoncello\ContainerLight\Container;
use Psr\Log\LoggerInterface;

/**
 * @package App
 */
trait SetUpCors
{
    /**
     * @param Container $container
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected static function setUpCors(Container $container)
    {
        $container[AnalyzerInterface::class] = function (ContainerInterface $container) {
            /** @var ConfigInterface $config */
            $config    = $container->get(ConfigInterface::class);
            $appConfig = $config->getConfig(Application::class);
            $strategy  = new Settings($config->getConfig(Settings::class));
            $analyzer  = Analyzer::instance($strategy);

            if ($appConfig[Application::KEY_IS_LOG_ENABLED] === true) {
                $logger = $container->get(LoggerInterface::class);
                $analyzer->setLogger($logger);
            }

            return $analyzer;
        };

        $container[CorsStorageInterface::class] = function () {
            return new CorsStorage();
        };
    }
}
