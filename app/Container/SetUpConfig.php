<?php namespace App\Container;

use App\Commands\CacheConfig as CC;
use Config\Core;
use Dotenv\Dotenv;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Config\ArrayConfig;
use Limoncello\Core\Config\ConfigManager;
use Limoncello\Core\Contracts\Config\ConfigInterface;

/**
 * @package App
 */
trait SetUpConfig
{
    /**
     * @param Container $container
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected static function setUpConfig(Container $container)
    {
        $container[ConfigInterface::class] = function () {
            $cachedConfig = '\\' . CC::CACHED_NAMESPACE . '\\' . CC::CACHED_CLASS . '::' . CC::CACHED_METHOD;
            if (is_callable($cachedConfig) === true) {
                $cached = call_user_func($cachedConfig);
                $config = new ArrayConfig($cached);
            } else {
                $dirWithEnvFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
                (new Dotenv($dirWithEnvFile))->load();
                $config = (new ConfigManager())->loadConfigs(Core::CONFIG_NAMESPACE, Core::CONFIG_DIR);
            }

            return $config;
        };
    }
}
