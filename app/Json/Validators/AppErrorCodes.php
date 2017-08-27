<?php namespace App\Json\Validators;

use Limoncello\Flute\Contracts\Validation\ErrorCodes;

/**
 * @package App
 */
interface AppErrorCodes extends ErrorCodes
{
    /** Custom error code */
    const IS_EMAIL = ErrorCodes::FLUTE_LAST + 1;
}
