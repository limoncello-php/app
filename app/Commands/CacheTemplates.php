<?php namespace App\Commands;

use Composer\Script\Event;
use Config\Services\Templates\Templates;
use Config\Services\Templates\TemplatesConfig;
use Limoncello\Templates\Scripts\BaseCacheTemplates;

/**
 * @package App
 */
class CacheTemplates extends BaseCacheTemplates
{
    use TemplatesConfig;

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function cache(Event $event)
    {
        CacheConfig::setInConfigCachingFlag($event);
        list($templatesPath, $cachePath) = static::getTemplatesConfig();
        static::cacheTemplates($event, realpath($templatesPath), realpath($cachePath), Templates::getTemplatesList());
    }

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function clear(Event $event)
    {
        CacheConfig::setInConfigCachingFlag($event);
        list(, $cachePath) = static::getTemplatesConfig();
        static::clearCacheFolder($event, realpath($cachePath));
    }

    /**
     * @return array
     */
    private static function getTemplatesConfig()
    {
        return (new static)->getConfig();
    }
}
