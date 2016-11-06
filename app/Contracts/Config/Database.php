<?php namespace App\Contracts\Config;

/**
 * @package App
 */
interface Database
{
    /** Config key */
    const CONNECTION_PARAMS = 0;

    /** Config key */
    const MODELS_LIST = self::CONNECTION_PARAMS + 1;
}
