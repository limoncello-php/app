<?php namespace Config;

/**
 * @package Config
 */
interface ConfigInterface
{
    /**
     * @return bool
     */
    public function useAppCache();

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param string     $serviceKey
     * @param string     $key
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function getConfigValue($serviceKey, $key, $default = null);

    /** Config key */
    const KEY_APP = 0;

    /** Config key */
    const KEY_APP_CACHE = self::KEY_APP + 1;

    /** Config key */
    const KEY_DB = self::KEY_APP_CACHE + 1;

    /** Config key */
    const KEY_CORS = self::KEY_DB + 1;

    /** Config key */
    const KEY_TEMPLATES = self::KEY_CORS + 1;

    /** Config key */
    const KEY_JSON_API = self::KEY_TEMPLATES + 1;

    // APP

    /** Config key */
    const KEY_APP_NAME = 0;

    /** Config key */
    const KEY_APP_DEBUG_MODE = self::KEY_APP_NAME + 1;

    /** Config key */
    const KEY_APP_ENABLE_LOGS = self::KEY_APP_DEBUG_MODE + 1;

    // APP CACHE

    /** Config key */
    const KEY_APP_CACHE_USE_CACHE = 0;
}
