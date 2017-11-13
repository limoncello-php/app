<?php namespace Settings;

use Dotenv\Dotenv;
use Limoncello\Application\Packages\Cors\CorsSettings;

/**
 * @package Settings
 */
class Cors extends CorsSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        return [
                static::KEY_LOG_IS_ENABLED => filter_var(getenv('APP_ENABLE_LOGS'), FILTER_VALIDATE_BOOLEAN),

                /**
                 * A list of allowed request origins (lower-cased, no trail slashes).
                 * Value `true` enables and value `null` disables origin.
                 * If all origins '*' are enabled all settings for other origins are ignored.
                 * For example, [
                 *     'http://example.com:123'       => true,
                 *     'http://evil.com'              => null,
                 *     static::VALUE_ALLOW_ORIGIN_ALL => null,
                 * ];
                 */
                static::KEY_ALLOWED_ORIGINS => [
                    static::VALUE_ALLOW_ORIGIN_ALL => true,
                ],

                /**
                 * A list of allowed request methods (case sensitive).
                 * Value `true` enables and value `null` disables method.
                 *
                 * For example,
                 *
                 * [
                 *     'GET'    => true,
                 *     'PATCH'  => true,
                 *     'POST'   => true,
                 *     'PUT'    => null,
                 *     'DELETE' => true,
                 * ];
                 *
                 * Security Note: you have to remember CORS is not access control system and you should not expect all
                 * cross-origin requests will have pre-flights. For so-called 'simple' methods with so-called 'simple'
                 * headers request will be made without pre-flight. Thus you can not restrict such requests with CORS
                 * and should use other means.
                 * For example method 'GET' without any headers or with only 'simple' headers will not have pre-flight
                 * request so disabling it will not restrict access to resource(s).
                 *
                 * You can read more on 'simple' methods at http://www.w3.org/TR/cors/#simple-method
                 */
                static::KEY_ALLOWED_METHODS => [
                    'GET'    => true,
                    'POST'   => true,
                    'PATCH'  => true,
                    'PUT'    => true,
                    'DELETE' => true,
                ],

                /**
                 * A list of allowed request headers (lower-cased).
                 * Value `true` enables and value `null` disables header.
                 *
                 * For example,
                 *
                 * $allowedHeaders = [
                 *     'content-type'            => true,
                 *     'x-custom-request-header' => null,
                 * ];
                 *
                 * Security Note: you have to remember CORS is not access control system and you should not expect all
                 * cross-origin requests will have pre-flights. For so-called 'simple' methods with so-called 'simple'
                 * headers request will be made without pre-flight. Thus you can not restrict such requests with CORS
                 * and should use other means.
                 * For example method 'GET' without any headers or with only 'simple' headers will not have pre-flight
                 * request so disabling it will not restrict access to resource(s).
                 *
                 * You can read more on 'simple' headers at http://www.w3.org/TR/cors/#simple-header
                 */
                static::KEY_ALLOWED_HEADERS => [
                    'accept'        => true,
                    'content-type'  => true,
                    'authorization' => true,
                    'origin'        => true,
                ],

                /**
                 * A list of headers (case insensitive) which will be made accessible to
                 * user agent (browser) in response. Value `true` enables and value `null` disables header.
                 *
                 * For example,
                 *
                 * [
                 *     'Content-Type'             => true,
                 *     'X-Custom-Response-Header' => true,
                 *     'X-Disabled-Header'        => null,
                 * ];
                 */
                static::KEY_EXPOSED_HEADERS => [
                    'content-type' => true,
                ],

            ] + parent::getSettings();
    }
}
