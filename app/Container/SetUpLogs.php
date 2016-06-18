<?php namespace App\Container;

use Config\ConfigInterface as C;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\SocketHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;
use Limoncello\ContainerLight\Container;
use Psr\Log\LoggerInterface;

/**
 * @package App
 */
trait SetUpLogs
{
    /**
     * @param Container $container
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected static function setUpFileLogs(Container $container)
    {
        $container[LoggerInterface::class] = function (Container $container) {
            /** @var C $config */
            $config = $container->get(C::class);

            $monolog = new Logger($config->getConfigValue(C::KEY_APP, C::KEY_APP_NAME, 'Limoncello'));
            if ($config->getConfigValue(C::KEY_APP, C::KEY_APP_ENABLE_LOGS, false) === true) {
                $logPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
                    . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR .
                    'limoncello.log';
                $handler = new StreamHandler($logPath, Logger::DEBUG);
                $handler->setFormatter(new LineFormatter(null, null, true, true));
                $handler->pushProcessor(new WebProcessor());
                $handler->pushProcessor(new UidProcessor());
            } else {
                $handler = new NullHandler();
            }
            $monolog->pushHandler($handler);

            return $monolog;
        };
    }

    /**
     * @param Container $container
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected static function setUpNetworkLogs(Container $container)
    {
        $container[LoggerInterface::class] = function (Container $container) {
            /** @var C $config */
            $config = $container->get(C::class);

            $monolog = new Logger($config->getConfigValue(C::KEY_APP, C::KEY_APP_NAME, 'Limoncello'));
            if ($config->getConfigValue(C::KEY_APP, C::KEY_APP_ENABLE_LOGS, false) === true) {
                $handler = new SocketHandler('udp://localhost:8081');
                $handler->pushProcessor(new WebProcessor());
                $handler->pushProcessor(new UidProcessor());
            } else {
                $handler = new NullHandler();
            }
            $monolog->pushHandler($handler);

            return $monolog;
        };
    }
}
