<?php namespace App\Validation;

use Limoncello\Flute\Contracts\Validation\ErrorCodes as BaseErrorCodes;

/**
 * @package App
 */
interface ErrorCodes extends BaseErrorCodes
{
    /** Custom error code */
    const IS_EMAIL = BaseErrorCodes::FLUTE_LAST + 1;

    /** Custom error code */
    const CONFIRMATION_SHOULD_MATCH_PASSWORD = self::IS_EMAIL + 1;
}
