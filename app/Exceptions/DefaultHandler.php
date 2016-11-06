<?php namespace App\Exceptions;

use App\Contracts\Config\Application as C;
use ErrorException;
use Exception;
use Interop\Container\ContainerInterface;
use Limoncello\Core\Contracts\Application\ExceptionHandlerInterface;
use Limoncello\Core\Contracts\Application\SapiInterface;
use Limoncello\Core\Contracts\Config\ConfigInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\TextResponse;

/**
 * @package App
 */
class DefaultHandler implements ExceptionHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function handleException(Exception $exception, SapiInterface $sapi, ContainerInterface $container)
    {
        $this->handle($exception, $sapi, $container);
    }

    /**
     * @inheritdoc
     */
    public function handleThrowable(Throwable $throwable, SapiInterface $sapi, ContainerInterface $container)
    {
        $this->handle($throwable, $sapi, $container);
    }

    /**
     * @inheritdoc
     */
    public function handleFatal(array $error, ContainerInterface $container)
    {
        $errorException = new ErrorException($error['message'], $error['type'], 1, $error['file'], $error['line']);
        $this->logException($errorException, $container, 'Fatal error');
    }

    /**
     * @param Exception|Throwable $exception
     * @param SapiInterface       $sapi
     * @param ContainerInterface  $container
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private function handle($exception, SapiInterface $sapi, ContainerInterface $container)
    {
        $appConfig = $container->get(ConfigInterface::class)->getConfig(C::class);

        $message = 'Internal Server Error';

        $this->logException($exception, $container, $message);

        if ($appConfig[C::KEY_IS_LOG_ENABLED] === true) {
            $run     = new Run();
            $handler = new PrettyPageHandler();

            // You can add app specific detailed data here
            $appSpecificDetails = [
                //"Important Data" => $container->get(SomeClass::class)->getImportantData(),
            ];

            $appName = $appConfig[C::KEY_NAME];
            if (empty($appSpecificDetails) === false) {
                $handler->addDataTable("$appName Details", $appSpecificDetails);
            }

            $handler->setPageTitle("Whoops! There was a problem with '$appName'.");
            $run->pushHandler($handler);

            $htmlMessage = $run->handleException($exception);
            $response    = new HtmlResponse($htmlMessage, 500);
        } else {
            $response = new TextResponse($message, 500);
        }

        $sapi->handleResponse($response);
    }

    /**
     * @param Exception          $exception
     * @param ContainerInterface $container
     * @param string             $message
     *
     * @return void
     */
    private function logException(Exception $exception, ContainerInterface $container, $message)
    {
        if ($container->has(LoggerInterface::class) === true) {
            /** @var LoggerInterface $logger */
            $logger = $container->get(LoggerInterface::class);
            $logger->critical($message, ['exception' => $exception]);
        }
    }
}
