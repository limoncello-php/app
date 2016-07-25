<?php namespace App\Api\Validation;

use App\Api\Validation\Locales\EnUsLocale;
use Limoncello\Validation\Contracts\MessageCodes;
use Limoncello\Validation\I18n\Translator as BaseTranslator;

/**
 * @package App
 */
class Translator extends BaseTranslator
{
    /** Custom error code */
    const IS_EMAIL = 1000001;

    /** Custom error code */
    const DB_UNIQUE = 1000002;

    /** Custom error code */
    const DB_EXISTS = 1000003;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(
            EnUsLocale::getLocaleCode(),
            EnUsLocale::getMessages(),
            EnUsLocale::getReplacements(),
            MessageCodes::INVALID_VALUE
        );
    }
}
