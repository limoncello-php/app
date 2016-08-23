<?php namespace App\I18n;

use Limoncello\Validation\Contracts\MessageCodes;

/**
 * Interface ValidationCodes
 */
interface ValidationCodes extends MessageCodes
{
    /** Custom error code */
    const IS_EMAIL = 1000001;

    /** Custom error code */
    const IS_URL = self::IS_EMAIL + 1;

    /** Custom error code */
    const DB_UNIQUE = self::IS_URL + 1;

    /** Custom error code */
    const DB_EXISTS = self::DB_UNIQUE + 1;
}
