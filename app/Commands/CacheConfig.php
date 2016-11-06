<?php namespace App\Commands;

use App\Container\SetUpConfig;
use Composer\Script\Event;
use Config\Core;
use Limoncello\AppCache\CacheScript;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Config\ConfigInterface;

/**
 * @package App
 */
class CacheConfig extends CacheScript
{
    use SetUpConfig;

    /** Cached class name */
    const CACHED_CLASS = 'Config';

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function cache(Event $event)
    {
        $container = new Container();
        self::setUpConfig($container);
        /** @var ConfigInterface $config */
        $config = $container->get(ConfigInterface::class);

        static::setInConfigCachingFlag($event);

        parent::cacheData(static::configToArray($config), $event);
    }

    /**
     * @param Event $event
     */
    public static function setInConfigCachingFlag(Event $event)
    {
        if (putenv(Core::IN_CONFIG_CACHING . '=1') !== true) {
            // some configs might expect to be informed that they are in config cache process.
            // if this flag is not set those configs could return not optimized settings.
            $event
                ->getIO()
                ->writeError("<warning>Setting environment flag failed. It might cause config cache issues.</warning>");
        }
    }

    /**
     * @param ConfigInterface $config
     *
     * @return array
     */
    private static function configToArray(ConfigInterface $config)
    {
        $result = [];

        foreach ($config->getConfigInterfaces() as $interface) {
            $result[$interface] = $config->getConfig($interface);
        }

        return $result;
    }
}
