<?php namespace Settings;

use Limoncello\Application\Packages\Commands\CommandSettings;

/**
 * @package Settings
 */
class Commands extends CommandSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        // this user will be used as a current while running console commands
        $userId = 5;

        return [
                static::KEY_IMPERSONATE_AS_USER_IDENTITY => $userId,
            ] + parent::getSettings();
    }
}
