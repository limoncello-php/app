<?php namespace Settings;

use Limoncello\Application\Packages\Monolog\MonologFileSettings;

/**
 * @package Settings
 */
class Monolog extends MonologFileSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        return [

                static::KEY_LOG_FOLDER => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'storage', 'logs']),

            ] + parent::getSettings();
    }
}
