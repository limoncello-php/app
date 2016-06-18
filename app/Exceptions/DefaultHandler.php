<?php namespace App\Exceptions;

use Config\ConfigInterface as C;
use Exception;
use Interop\Container\ContainerInterface;
use Limoncello\Core\Contracts\Application\ExceptionHandlerInterface;
use Limoncello\Core\Contracts\Application\SapiInterface;
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
     * @param Exception          $exception
     * @param SapiInterface      $sapi
     * @param ContainerInterface $container
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function handleException(Exception $exception, SapiInterface $sapi, ContainerInterface $container)
    {
        $this->handle($exception, $sapi, $container);
    }

    /**
     * @param Throwable          $throwable
     * @param SapiInterface      $sapi
     * @param ContainerInterface $container
     */
    public function handleThrowable(Throwable $throwable, SapiInterface $sapi, ContainerInterface $container)
    {
        $this->handle($throwable, $sapi, $container);
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
        /** @var C $config */
        $config       = $container->get(C::class);
        $debugEnabled = $config->getConfigValue(C::KEY_APP, C::KEY_APP_DEBUG_MODE);

        $message  = 'Internal Server Error';

        if ($container->has(LoggerInterface::class) === true) {
            /** @var LoggerInterface $logger */
            $logger = $container->get(LoggerInterface::class);
            $logger->critical($message, ['exception' => $exception]);
        }

        if ($debugEnabled === true) {
            $run     = new Run();
            $handler = new PrettyPageHandler();

            // You can add app specific detailed data here
            $appSpecificDetails = [
                //"Important Data" => $container->get(SomeClass::class)->getImportantData(),
            ];

            $appName = $config->getConfigValue(C::KEY_APP, C::KEY_APP_NAME);
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
}
