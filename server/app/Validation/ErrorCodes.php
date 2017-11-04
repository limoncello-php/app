<?php namespace App\Validation;

use Limoncello\Flute\Contracts\Validation\ErrorCodes as BaseErrorCodes;

/**
 * @package App
 */
interface ErrorCodes extends BaseErrorCodes
{
    /** Custom error code */
    const IS_EMAIL = BaseErrorCodes::FLUTE_LAST + 1;
}
