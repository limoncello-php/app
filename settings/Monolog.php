<?php namespace Settings;

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
    public function get(): array
    {
        $logPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'storage', 'logs', 'limoncello.log']);

        return [
            static::KEY_IS_ENABLED => true,
            static::KEY_LOG_PATH   => $logPath,
            static::KEY_LOG_LEVEL  => Logger::DEBUG,
        ];
    }
}
