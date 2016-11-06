<?php namespace App\Commands;

use App\Application;
use Composer\Script\Event;
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

        parent::cacheData($routes, $event);
    }
}
