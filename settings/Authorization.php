<?php namespace Settings;

use Limoncello\Application\Packages\Authorization\AuthorizationSettings;

/**
 * @package Settings
 */
class Authorization extends AuthorizationSettings
{
    /**
     * @inheritdoc
     */
    public function get(): array
    {
        $settings = parent::get();

        $settings[static::KEY_LOG_IS_ENABLED] = false;

        return $settings;
    }

    /**
     * @return string
     */
    protected function getPoliciesPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Authorization', '*.php']);
    }
}
