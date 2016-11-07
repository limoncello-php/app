<?php namespace Config;

use Limoncello\Core\Config\ArrayConfig;
use Monolog\Logger;

/**
 * @package Config
 */
class Application extends ArrayConfig
{
    /** Config key */
    const KEY_NAME = 0;

    /** Config key */
    const KEY_IS_LOG_ENABLED = self::KEY_NAME + 1;

    /** Config key */
    const KEY_LOG_PATH = self::KEY_IS_LOG_ENABLED + 1;

    /** Config key */
    const KEY_LOG_LEVEL = self::KEY_LOG_PATH + 1;

    /** Config key */
    const KEY_IS_DEBUG = self::KEY_LOG_LEVEL + 1;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $isInConfigCachingProcess = getenv(Core::IN_CONFIG_CACHING) !== false;

        $logPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'limoncello.log';

        parent::__construct([self::class => [
            self::KEY_NAME           => getenv('APP_NAME'),
            self::KEY_IS_LOG_ENABLED => getenv('APP_ENABLE_LOGS'),
            self::KEY_LOG_PATH       => $logPath,
            self::KEY_LOG_LEVEL      => Logger::DEBUG,
            self::KEY_IS_DEBUG       => !$isInConfigCachingProcess,
        ]]);
    }
}
