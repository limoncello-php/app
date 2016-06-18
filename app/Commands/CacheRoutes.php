<?php namespace App\Commands;

use App\Application;
use Composer\Script\Event;
use Config\Config;
use Limoncello\AppCache\CacheScript;

/**
 * @package App
 */
class CacheRoutes extends CacheScript
{
    /** Cached class name */
    const CACHED_CLASS = 'Routes';

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function cache(Event $event)
    {
        $app    = new Application();
        $routes = $app->getRoutes();

        $config = new Config();
        if ($config->useAppCache() !== true) {
            $event
                ->getIO()
                ->writeError("<warning>Use cache config option is set to OFF. Cache will not be used.</warning>");
        }

        parent::cacheData($routes, $event);
    }
}
