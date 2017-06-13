<?php namespace Settings;

use Dotenv\Dotenv;
use Limoncello\Application\Packages\PDO\PdoSettings;
use PDO;

/**
 * @package Settings
 */
class PdoDatabase extends PdoSettings
{
    /**
     * @inheritdoc
     */
    public function get(): array
    {
        (new Dotenv(__DIR__ . DIRECTORY_SEPARATOR . '..'))->load();

        return [
            static::KEY_USER_NAME         => getenv('PDO_USER_NAME'),
            static::KEY_PASSWORD          => getenv('PDO_USER_PASSWORD'),
            static::KEY_CONNECTION_STRING => getenv('PDO_CONNECTION_STRING'),
            static::KEY_OPTIONS           => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ],
        ];
    }
}
