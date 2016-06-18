<?php namespace Config;

use Config\ConfigInterface as C;
use Config\Services\App\AppConfig;
use Config\Services\App\AppCacheConfig;
use Config\Services\Cors\CorsConfig;
use Config\Services\Database\DatabaseConfig;
use Config\Services\JsonApi\JsonApiConfig;
use Config\Services\Templates\TemplatesConfig;

/**
 * @package Config
 */
class Config implements C
{
    use AppConfig, AppCacheConfig, CorsConfig, DatabaseConfig, JsonApiConfig, TemplatesConfig {
        AppConfig::getConfig as private getAppConfig;
        AppCacheConfig::getConfig as private getAppCacheConfig;
        CorsConfig::getConfig as private getCorsConfig;
        DatabaseConfig::getConfig as private getDatabaseConfig;
        JsonApiConfig::getConfig as private getJsonApiConfig;
        TemplatesConfig::getConfig as private getTemplatesConfig;
    }

    /**
     * @var array|null
     */
    private $config = null;

    /**
     * @inheritdoc
     */
    public function useAppCache()
    {
        $config = $this->getAppCacheConfig();
        $value  = $config[C::KEY_APP_CACHE_USE_CACHE];

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        if ($this->config === null) {
            $this->config = $this->readConfig();
        }

        return $this->config;
    }

    /**
     * @inheritdoc
     */
    public function getConfigValue($serviceKey, $key, $default = null)
    {
        $config = $this->getConfig();
        $value  = isset($config[$serviceKey][$key]) === true ? $config[$serviceKey][$key] : $default;

        return $value;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function readConfig()
    {
        return [
            C::KEY_APP       => $this->getAppConfig(),
            C::KEY_DB        => $this->getDatabaseConfig(),
            C::KEY_CORS      => $this->getCorsConfig(),
            C::KEY_JSON_API  => $this->getJsonApiConfig(),
            C::KEY_TEMPLATES => $this->getTemplatesConfig(),
        ];
    }
}
