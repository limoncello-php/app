<?php namespace Config;

use App\Contracts\Config\Application as C;
use Limoncello\Core\Config\ArrayConfig;
use Monolog\Logger;

/**
 * @package Config
 */
class Application extends ArrayConfig
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $isInConfigCachingProcess = getenv(Core::IN_CONFIG_CACHING) !== false;

        $logPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'limoncello.log';

        parent::__construct([C::class => [
            C::KEY_NAME           => getenv('APP_NAME'),
            C::KEY_IS_LOG_ENABLED => getenv('APP_ENABLE_LOGS'),
            C::KEY_LOG_PATH       => $logPath,
            C::KEY_LOG_LEVEL      => Logger::DEBUG,
            C::KEY_IS_DEBUG       => !$isInConfigCachingProcess,
        ]]);
    }
}
