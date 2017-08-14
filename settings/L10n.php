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
    protected function getDefaultLocale(): string
    {
        return 'en';
    }

    /**
     * @inheritdoc
     */
    protected function getLocalesPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'resources', 'messages']);
    }
}
