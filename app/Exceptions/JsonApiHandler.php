<?php namespace App\Exceptions;

use Config\ConfigInterface as C;
use ErrorException;
use Exception;
use Interop\Container\ContainerInterface;
use Limoncello\Core\Contracts\Application\ExceptionHandlerInterface;
use Limoncello\Core\Contracts\Application\SapiInterface;
use Limoncello\JsonApi\Contracts\Encoder\EncoderInterface;
use Limoncello\JsonApi\Contracts\Http\Cors\CorsStorageInterface;
use Limoncello\JsonApi\Http\JsonApiResponse;
use Neomerx\JsonApi\Document\Error;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class JsonApiHandler implements ExceptionHandlerInterface
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
        $message = 'Internal Server Error';

        $this->logException($exception, $container, $message);

        // compose JSON API Error with appropriate level of details
        if ($exception instanceof JsonApiException) {
            /** @var JsonApiException $exception */
            $errors   = $exception->getErrors();
            $httpCode = $exception->getHttpCode();
        } else {
            // we assume that 'normal' should be JsonApiException so anything else is 500 error code
            $httpCode = 500;
            $details  = null;
            /** @var C $config */
            $config       = $container->get(C::class);
            $debugEnabled = $config->getConfigValue(C::KEY_APP, C::KEY_APP_DEBUG_MODE);
            if ($debugEnabled === true) {
                $message = $exception->getMessage();
                $details = (string)$exception;
            }
            $errors = new ErrorCollection();
            $errors->add(new Error(null, null, $httpCode, null, $message, $details));
        }

        // encode the error and send to client
        /** @var EncoderInterface $encoder */
        $encoder     = $container->get(EncoderInterface::class);
        $content     = $encoder->encodeErrors($errors);
        /** @var CorsStorageInterface $corsStorage */
        $corsStorage = $container->get(CorsStorageInterface::class);
        $response    = new JsonApiResponse($content, $httpCode, $corsStorage->getHeaders());
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
        // log the error if necessary (you can list here all error classes that should not be logged)
        $ignoredErrorTypes = [
            JsonApiException::class,
        ];

        if (in_array(get_class($exception), $ignoredErrorTypes, true) === false &&
            $container->has(LoggerInterface::class) === true
        ) {
            /** @var LoggerInterface $logger */
            $logger = $container->get(LoggerInterface::class);
            $logger->critical($message, ['exception' => $exception]);
        }
    }
}
