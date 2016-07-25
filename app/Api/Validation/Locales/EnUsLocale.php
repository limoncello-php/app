<?php namespace App\Api\Validation\Locales;

use App\Api\Validation\Translator;
use Limoncello\Validation\I18n\Locales\EnUsLocale as BaseEnUsLocale;

/**
 * @package App\Api
 */
class EnUsLocale extends BaseEnUsLocale
{
    /**
     * @inheritdoc
     */
    public static function getMessages()
    {
        $messages = [
            Translator::IS_EMAIL  => "The `{0}` value should be a valid email address.",
            Translator::DB_EXISTS => "The `{0}` value should exist.",
            Translator::DB_UNIQUE => "The `{0}` value should be unique.",
        ];

        return (parent::getMessages() + $messages);
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
