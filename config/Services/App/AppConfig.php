<?php namespace Config\Services\App;

use App\Commands\CacheConfig;
use Config\ConfigInterface as C;

/**
 * @package Config
 */
trait AppConfig
{
    /**
     * @return array
     */
    protected function getConfig()
    {
        $isInConfigCachingProcess = getenv(CacheConfig::IN_CONFIG_CACHING) !== false;

        return [
            C::KEY_APP_NAME        => getenv('APP_NAME'),
            C::KEY_APP_DEBUG_MODE  => !$isInConfigCachingProcess,
            C::KEY_APP_ENABLE_LOGS => false,
        ];
    }
}
