<?php namespace Settings;

use App\Authentication\OAuth;
use App\Data\Models\User;
use Dotenv\Dotenv;
use Limoncello\Passport\Package\PassportSettings;

/**
 * @package Settings
 */
class Passport extends PassportSettings
{
    /** Config key */
    const KEY_DEFAULT_CLIENT_NAME = self::KEY_LAST + 1;

    /** Config key */
    const KEY_DEFAULT_CLIENT_REDIRECT_URIS = self::KEY_DEFAULT_CLIENT_NAME + 1;

    /** URI to handle OAuth scope approval for code and implicit grants. */
    const APPROVAL_URI = 'oauth-scope-approval';

    /** URI to handle OAuth critical errors such as invalid client ID or unsupported grant types. */
    const ERROR_URI = 'oauth-error';

    /** Default OAuth client ID */
    const DEFAULT_CLIENT_ID = 'default_client';

    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        $isLogEnabled = filter_var(getenv('APP_ENABLE_LOGS'), FILTER_VALIDATE_BOOLEAN);

        return [

                static::KEY_IS_LOG_ENABLED                   => $isLogEnabled,
                static::KEY_DEFAULT_CLIENT_NAME              => getenv('APP_NAME'),
                static::KEY_DEFAULT_CLIENT_ID                => static::DEFAULT_CLIENT_ID,
                static::KEY_TOKEN_CUSTOM_PROPERTIES_PROVIDER => OAuth::TOKEN_CUSTOM_PROPERTIES_PROVIDER,
                static::KEY_APPROVAL_URI_STRING              => static::APPROVAL_URI,
                static::KEY_ERROR_URI_STRING                 => static::ERROR_URI,
                static::KEY_DEFAULT_CLIENT_REDIRECT_URIS     => [],
                static::KEY_USER_TABLE_NAME                  => User::TABLE_NAME,
                static::KEY_USER_PRIMARY_KEY_NAME            => User::FIELD_ID,
                static::KEY_USER_CREDENTIALS_VALIDATOR       => OAuth::USER_VALIDATOR,
                static::KEY_USER_SCOPE_VALIDATOR             => OAuth::SCOPE_VALIDATOR,

            ] + parent::getSettings();
    }
}
