<?php namespace Settings;

use Dotenv\Dotenv;
use Limoncello\Application\Packages\Monolog\MonologFileSettings;
use Monolog\Logger;

/**
 * @package Settings
 */
class Monolog extends MonologFileSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        $isDebug = filter_var(getenv('APP_IS_DEBUG'), FILTER_VALIDATE_BOOLEAN);

        return [

                static::KEY_LOG_LEVEL  => $isDebug === true ? Logger::DEBUG : Logger::INFO,
                static::KEY_LOG_FOLDER => implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'storage', 'logs']),
                static::KEY_IS_ENABLED => filter_var(getenv('APP_ENABLE_LOGS'), FILTER_VALIDATE_BOOLEAN),

            ] + parent::getSettings();
    }
}
