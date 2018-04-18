<?php namespace Settings;

use Dotenv\Dotenv;
use Limoncello\Application\Packages\Data\DoctrineSettings;

/**
 * @package Settings
 */
class Doctrine extends DoctrineSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        $dbFile = getenv('DB_FILE');
        $dbPath = empty($dbFile) === true ? null : implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'storage', $dbFile]);

        return [

            static::KEY_DATABASE_NAME => getenv('DB_DATABASE_NAME'),
            static::KEY_USER_NAME     => getenv('DB_USER_NAME'),
            static::KEY_PASSWORD      => getenv('DB_USER_PASSWORD'),
            static::KEY_HOST          => getenv('DB_HOST'),
            static::KEY_PORT          => getenv('DB_PORT'),
            static::KEY_CHARSET       => getenv('DB_CHARSET'),
            static::KEY_DRIVER        => getenv('DB_DRIVER'),
            static::KEY_PATH          => $dbPath,
            static::KEY_EXEC          => [
                'PRAGMA foreign_keys = ON;'
            ],

        ] + parent::getSettings();
    }
}
