<?php namespace App\Container;

use App\Commands\CacheConfig as CC;
use Config\Config;
use Config\ConfigInterface as C;
use Dotenv\Dotenv;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 */
trait SetUpConfig
{
    /**
     * @param Container $container
     *
     * @return void
     */
    protected static function setUpConfig(Container $container)
    {
        $container[C::class] = function () {
            $config       = new Config();
            $cachedConfig = '\\' . CC::CACHED_NAMESPACE . '\\' . CC::CACHED_CLASS . '::' . CC::CACHED_METHOD;
            if ($config->useAppCache() === true && is_callable($cachedConfig) === true) {
                $cached = call_user_func($cachedConfig);
                $config->setConfig($cached);
            } else {
                $envDir  = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
                $envFile = getenv('ENV_FILE');
                $dotEnv  = $envFile === false ? new Dotenv($envDir) : new Dotenv($envDir, $envFile);
                $dotEnv->load();
            }

            return $config;
        };
    }
}
