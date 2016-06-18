<?php namespace App\Commands;

use App\Container\SetUpConfig;
use Composer\Script\Event;
use Config\ConfigInterface;
use Limoncello\AppCache\CacheScript;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 */
class CacheConfig extends CacheScript
{
    use SetUpConfig;

    /** Cached class name */
    const CACHED_CLASS = 'Config';

    /**
     * This environment variable will be set during config caching.
     */
    const IN_CONFIG_CACHING = 'IN_CONFIG_CACHING';

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function cache(Event $event)
    {
        $container = new Container();
        self::setUpConfig($container);
        $config = $container->get(ConfigInterface::class);

        if ($config->useAppCache() !== true) {
            $event
                ->getIO()
                ->writeError("<warning>Use cache config option is set to OFF. Cache will not be used.</warning>");
        }

        static::setInConfigCachingFlag($event);

        parent::cacheData($config->readConfig(), $event);
    }

    /**
     * @param Event $event
     */
    public static function setInConfigCachingFlag(Event $event)
    {
        if (putenv(static::IN_CONFIG_CACHING . '=1') !== true) {
            // some configs might expect to be informed that they are in config cache process.
            // if this flag is not set those configs could return not optimized settings.
            $event
                ->getIO()
                ->writeError("<warning>Setting environment flag failed. It might cause config cache issues.</warning>");
        }
    }
}
