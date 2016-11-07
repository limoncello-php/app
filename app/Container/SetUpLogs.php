<?php namespace App\Container;

use Config\Application as C;
use Limoncello\Core\Contracts\Config\ConfigInterface;
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
            $appConfig = $container->get(ConfigInterface::class)->getConfig(C::class);
            $monolog   = new Logger($appConfig[C::KEY_NAME]);
            if ($appConfig[C::KEY_IS_LOG_ENABLED] === true) {
                $handler = new StreamHandler($appConfig[C::KEY_LOG_PATH], $appConfig[C::KEY_LOG_LEVEL]);
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
            $appConfig = $container->get(ConfigInterface::class)->getConfig(C::class);
            $monolog   = new Logger($appConfig[C::KEY_NAME]);
            if ($appConfig[C::KEY_IS_LOG_ENABLED] === true) {
                $handler = new SocketHandler('udp://localhost:8081', $appConfig[C::KEY_LOG_LEVEL]);
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
