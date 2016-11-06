<?php namespace App\Contracts\Config;

/**
 * @package App
 */
interface Application
{
    /** Config key */
    const KEY_NAME = 0;

    /** Config key */
    const KEY_IS_LOG_ENABLED = self::KEY_NAME + 1;

    /** Config key */
    const KEY_LOG_PATH = self::KEY_IS_LOG_ENABLED + 1;

    /** Config key */
    const KEY_LOG_LEVEL = self::KEY_LOG_PATH + 1;

    /** Config key */
    const KEY_IS_DEBUG = self::KEY_LOG_LEVEL + 1;
}
