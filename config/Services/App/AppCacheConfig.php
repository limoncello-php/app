<?php namespace Config\Services\App;

use Config\ConfigInterface as C;

/**
 * @package Config
 */
trait AppCacheConfig
{
    /**
     * @return array
     */
    protected function getConfig()
    {
        // This config branch is the only that is not cached.
        return [
            C::KEY_APP_CACHE_USE_CACHE => true,
        ];
    }
}
