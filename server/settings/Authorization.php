<?php namespace Settings;

use Dotenv\Dotenv;
use Limoncello\Application\Packages\Authorization\AuthorizationSettings;

/**
 * @package Settings
 */
class Authorization extends AuthorizationSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        return [

                static::KEY_LOG_IS_ENABLED  => filter_var(getenv('APP_ENABLE_LOGS'), FILTER_VALIDATE_BOOLEAN),
                static::KEY_POLICIES_FOLDER => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Authorization']),

            ] + parent::getSettings();
    }
}
