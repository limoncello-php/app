<?php namespace App\Contracts\Config;

/**
 * @package App
 */
interface Templates
{
    /** Config key */
    const TEMPLATES_LIST = 0;

    /** Config key */
    const TEMPLATES_FOLDER = self::TEMPLATES_LIST + 1;

    /** Config key */
    const CACHE_FOLDER = self::TEMPLATES_FOLDER + 1;
}
