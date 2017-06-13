<?php namespace Settings;

use Limoncello\Application\Packages\Cors\CorsSettings;

/**
 * @package Settings
 */
class Cors extends CorsSettings
{
    /**
     * @inheritdoc
     */
    public function get(): array
    {
        $defaults = parent::get();

        /**
         * Array should be in parse_url() result format.
         * @see http://php.net/manual/function.parse-url.php
         */
        $defaults[static::KEY_SERVER_ORIGIN] = [
            static::KEY_SERVER_ORIGIN_SCHEME => Application::ORIGIN_SCHEME,
            static::KEY_SERVER_ORIGIN_HOST   => Application::ORIGIN_HOST,
            static::KEY_SERVER_ORIGIN_PORT   => (string)Application::ORIGIN_PORT,
        ];
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
        $defaults[static::KEY_ALLOWED_ORIGINS] = [
            static::VALUE_ALLOW_ORIGIN_ALL => true,
        ];
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
        $defaults[static::KEY_ALLOWED_METHODS] = [
            'GET'    => true,
            'POST'   => true,
            'PATCH'  => true,
            'PUT'    => true,
            'DELETE' => true,
        ];
        /**
         * A list of allowed request headers (lower-cased). Value `true` enables and value `null` disables header.
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
        $defaults[static::KEY_ALLOWED_HEADERS] = [
            'accept'        => true,
            'content-type'  => true,
            'authorization' => true,
            'origin'        => true,
        ];
        /**
         * A list of headers (case insensitive) which will be made accessible to user agent (browser) in response.
         * Value `true` enables and value `null` disables header.
         *
         * For example,
         *
         * [
         *     'Content-Type'             => true,
         *     'X-Custom-Response-Header' => true,
         *     'X-Disabled-Header'        => null,
         * ];
         */
        $defaults[static::KEY_EXPOSED_HEADERS] = [
            'content-type' => true,
        ];

        return $defaults;
    }
}
