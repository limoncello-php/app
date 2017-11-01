<?php namespace Settings;

use Limoncello\Application\Packages\L10n\L10nSettings;

/**
 * @package Settings
 */
class L10n extends L10nSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        return [

                static::KEY_LOCALES_FOLDER => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'resources', 'messages']),

            ] + parent::getSettings();
    }
}
