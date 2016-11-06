<?php namespace App;

use App\Commands\CacheRoutes as CR;
use App\Container\Factory;
use App\Exceptions\DefaultHandler;
use App\Http\Routes;
use ErrorException;
use Exception;
use Interop\Container\ContainerInterface;
use Limoncello\Core\Application\Sapi;
use Limoncello\Core\Contracts\Application\ExceptionHandlerInterface;
use Limoncello\Core\Contracts\Application\SapiInterface;
use Throwable;
use Zend\Diactoros\Response\SapiEmitter;

/**
 * @package App
 */
class Application extends \Limoncello\Core\Application\Application
{
    use Factory, Routes {
        Routes::getRoutes as private getRoutesGroup;
    }

    /**
     * @inheritdoc
     */
    public function __construct(SapiInterface $sapi = null)
    {
        if ($sapi === null) {
            $sapi = new Sapi(new SapiEmitter());
            $this->setSapi($sapi);
        }
    }

    /**
     * @inheritdoc
     */
    protected function setUpExceptionHandler(SapiInterface $sapi, ContainerInterface $container)
    {
        error_reporting(E_ALL);

        $createHandler = function () use ($container) {
            $has     = $container->has(ExceptionHandlerInterface::class);
            $handler = $has === true ? $container->get(ExceptionHandlerInterface::class) : new DefaultHandler();
            return $handler;
        };

        $throwableHandler = function (Throwable $throwable) use ($sapi, $container, $createHandler) {
            /** @var ExceptionHandlerInterface $handler */
            $handler = $createHandler();
            $handler->handleThrowable($throwable, $sapi, $container);
        };

        $exceptionHandler = function (Exception $exception) use ($sapi, $container, $createHandler) {
            /** @var ExceptionHandlerInterface $handler */
            $handler = $createHandler();
            $handler->handleException($exception, $sapi, $container);
        };

        set_exception_handler(PHP_MAJOR_VERSION >= 7 ? $throwableHandler : $exceptionHandler);

        set_error_handler(function ($severity, $message, $fileName, $lineNumber) {
            $errorException = new ErrorException($message, 0, $severity, $fileName, $lineNumber);
            throw $errorException;
        });

        // handle fatal error
        register_shutdown_function(function () use ($container, $createHandler) {
            $error = error_get_last();
            if ($error !== null && ((int)$error['type'] & (E_ERROR | E_COMPILE_ERROR) )) {
                /** @var ExceptionHandlerInterface $handler */
                $handler = $createHandler();
                $handler->handleFatal($error, $container);
            }
        });
    }

    /**
     * @inheritdoc
     */
    public function getRoutes()
    {
        $topGroup = $this->getRoutesGroup();
        $router   = $this->getRouter();
        $routes   = $router->getCachedRoutes($topGroup);

        return $routes;
    }

    /**
     * @inheritdoc
     */
    protected function getRoutesData()
    {
        $cachedRoutes = '\\' . CR::CACHED_NAMESPACE . '\\' . CR::CACHED_CLASS . '::' . CR::CACHED_METHOD;
        if (is_callable($cachedRoutes) === true) {
            $routes = call_user_func($cachedRoutes);
            return $routes;
        }

        return $this->getRoutes();
    }
}
