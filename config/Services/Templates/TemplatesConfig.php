<?php namespace Config\Services\Templates;

use App\Commands\CacheConfig;

/**
 * @package Config
 */
trait TemplatesConfig
{
    /**
     * @return array
     */
    protected function getConfig()
    {
        $isInConfigCachingProcess = getenv(CacheConfig::IN_CONFIG_CACHING) !== false;
        $cacheFolder = $isInConfigCachingProcess === true ? TemplatesInterface::CACHE_FOLDER : null;

        return [TemplatesInterface::TEMPLATES_FOLDER, $cacheFolder];
    }
}
