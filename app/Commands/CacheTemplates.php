<?php namespace App\Commands;

use App\Container\SetUpConfig;
use Composer\Script\Event;
use Config\Templates as C;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Config\ConfigInterface;
use Limoncello\Templates\Scripts\BaseCacheTemplates;

/**
 * @package App
 */
class CacheTemplates extends BaseCacheTemplates
{
    use SetUpConfig;

    /**
     * @param Event $event
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function cache(Event $event)
    {
        CacheConfig::setInConfigCachingFlag($event);
        $tplConfig = static::getTemplatesConfig();
        static::cacheTemplates(
            $event,
            $tplConfig[C::TEMPLATES_FOLDER],
            $tplConfig[C::CACHE_FOLDER],
            $tplConfig[C::TEMPLATES_LIST]
        );
    }

    /**
     * @param Event $event
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function clear(Event $event)
    {
        CacheConfig::setInConfigCachingFlag($event);
        $tplConfig = static::getTemplatesConfig();
        static::clearCacheFolder($event, $tplConfig[C::CACHE_FOLDER]);
    }

    /**
     * @return array
     */
    private static function getTemplatesConfig()
    {
        $container = new Container();
        self::setUpConfig($container);
        $tplConfig = $container->get(ConfigInterface::class)->getConfig(C::class);

        return $tplConfig;
    }
}
