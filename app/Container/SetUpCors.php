<?php namespace App\Container;

use Config\ConfigInterface as C;
use Interop\Container\ContainerInterface;
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
            /** @var C $config */
            $config       = $container->get(C::class);
            $corsSettings = $config->getConfig()[C::KEY_CORS];
            $strategy     = new Settings($corsSettings);

            $analyzer = Analyzer::instance($strategy);

            if ($config->getConfigValue(C::KEY_APP, C::KEY_APP_ENABLE_LOGS) === true) {
                $logger = $container->get(LoggerInterface::class);
                $analyzer->setLogger($logger);
            }

            return $analyzer;
        };

        $container[CorsStorageInterface::class] = new CorsStorage();
    }
}
