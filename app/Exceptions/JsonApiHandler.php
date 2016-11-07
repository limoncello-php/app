<?php namespace App\Exceptions;

use Config\Application as C;
use ErrorException;
use Exception;
use Interop\Container\ContainerInterface;
use Limoncello\Core\Contracts\Application\ExceptionHandlerInterface;
use Limoncello\Core\Contracts\Application\SapiInterface;
use Limoncello\Core\Contracts\Config\ConfigInterface;
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
     * The following error classes (Exceptions and Throwables) will not be logged.
     *
     * @var string[]
     */
    private static $ignoredErrorClasses = [
        JsonApiException::class,
    ];
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
        $this->logError($errorException, $container, 'Fatal error');
    }

    /**
     * @param Exception|Throwable $error
     * @param SapiInterface       $sapi
     * @param ContainerInterface  $container
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private function handle($error, SapiInterface $sapi, ContainerInterface $container)
    {
        $message = 'Internal Server Error';

        $this->logError($error, $container, $message);

        // compose JSON API Error with appropriate level of details
        if ($error instanceof JsonApiException) {
            /** @var JsonApiException $error */
            $errors   = $error->getErrors();
            $httpCode = $error->getHttpCode();
        } else {
            // we assume that 'normal' should be JsonApiException so anything else is 500 error code
            $httpCode = 500;
            $details  = null;
            $appConfig = $container->get(ConfigInterface::class)->getConfig(C::class);
            if ($appConfig[C::KEY_IS_LOG_ENABLED] === true) {
                $message = $error->getMessage();
                $details = (string)$error;
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
     * @param Exception|Throwable $error
     * @param ContainerInterface  $container
     * @param string              $message
     *
     * @return void
     */
    private function logError($error, ContainerInterface $container, $message)
    {
        if (in_array(get_class($error), static::$ignoredErrorClasses) === false &&
            $container->has(LoggerInterface::class) === true
        ) {
            /** @var LoggerInterface $logger */
            $logger = $container->get(LoggerInterface::class);
            $logger->critical($message, ['error' => $error]);
        }
    }
}
