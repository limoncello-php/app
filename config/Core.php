<?php namespace Config;

/**
 * @package Config
 */
class Core
{
    /**
     * Directory for storing configs.
     */
    const CONFIG_DIR = __DIR__;

    /**
     * Namespace for config classes.
     */
    const CONFIG_NAMESPACE = __NAMESPACE__;

    /**
     * This environment variable will be set during config caching.
     */
    const IN_CONFIG_CACHING = 'IN_CONFIG_CACHING';
}
