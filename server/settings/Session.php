<?php namespace Settings;

use Limoncello\Application\Packages\Session\SessionSettings;

/**
 * @package Settings
 */
class Session extends SessionSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        // For the full list of available options
        // - @see SessionSettings
        // - @link http://php.net/manual/en/session.configuration.php

        return [

                static::KEY_COOKIE_SECURE   => '',
                static::KEY_COOKIE_LIFETIME => '0',

            ] + parent::getSettings();
    }
}
