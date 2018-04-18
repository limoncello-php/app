<?php namespace Settings;

use Dotenv\Dotenv;
use Limoncello\Application\Packages\Authorization\AuthorizationSettings;

/**
 * @package Settings
 */
class Authorization extends AuthorizationSettings
{
    /** Settings key. If auth cooke should be sent only over secured (https) connection. */
    const KEY_AUTH_COOKIE_ONLY_OVER_HTTPS = self::KEY_LAST + 1;

    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        return [

                static::KEY_LOG_IS_ENABLED  => filter_var(getenv('APP_ENABLE_LOGS'), FILTER_VALIDATE_BOOLEAN),
                static::KEY_POLICIES_FOLDER => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Authorization']),

                static::KEY_AUTH_COOKIE_ONLY_OVER_HTTPS => filter_var(getenv('APP_AUTH_COOKIE_ONLY_OVER_HTTPS'), FILTER_VALIDATE_BOOLEAN),

            ] + parent::getSettings();
    }
}
