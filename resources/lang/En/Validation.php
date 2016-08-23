<?php namespace App\I18n\En;

use App\I18n\ValidationCodes;
use Limoncello\Validation\I18n\Locales\EnUsLocale as BaseEnUsLocale;

/**
 * @package App\Api
 */
class Validation extends BaseEnUsLocale
{
    /**
     * @inheritdoc
     */
    public static function getMessages()
    {
        return (parent::getMessages() + [
            ValidationCodes::IS_EMAIL  => "The `{0}` value should be a valid email address.",
            ValidationCodes::IS_URL    => "The `{0}` value should be a valid URL.",
            ValidationCodes::DB_EXISTS => "The `{0}` value should exist.",
            ValidationCodes::DB_UNIQUE => "The `{0}` value should be unique.",
        ]);
    }

    /**
     * @return array
     */
    public static function getReplacements()
    {
        return [
            // 'email_address' => 'Email Address',
        ];
    }
}
