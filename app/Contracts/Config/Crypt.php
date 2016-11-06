<?php namespace App\Contracts\Config;

/**
 * @package App
 */
interface Crypt
{
    /** Config key */
    const HASH_ALGORITHM = 0;

    /** Config key */
    const HASH_COST = self::HASH_ALGORITHM + 1;
}
