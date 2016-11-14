<?php namespace Tests;

use App\Exceptions\JsonApiHandler;
use Config\Application;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Application\SapiInterface;
use Limoncello\Core\Contracts\Config\ConfigInterface;
use Limoncello\JsonApi\Contracts\Encoder\EncoderInterface;
use Limoncello\JsonApi\Contracts\Http\Cors\CorsStorageInterface;
use Limoncello\JsonApi\Http\Cors\CorsStorage;
use LogicException;
use Mockery;
use Mockery\Mock;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * @package Tests
 */
class ExceptionsHandlerTest extends TestCase
{
    /**
     * JSON API exception test.
     */
    public function testJsonApiHandler1()
    {
        $errors    = new ErrorCollection();
        $exception = new JsonApiException($errors);
        $handler   = new JsonApiHandler();
        $sapi      = Mockery::mock(SapiInterface::class);
        $encoder   = Mockery::mock(EncoderInterface::class);
        $container = new Container();
        $container[EncoderInterface::class] = $encoder;

        /** @var Mock $sapi */
        /** @var Mock $encoder */

        $sapi->shouldReceive('handleResponse')->once()->withAnyArgs()->andReturnUndefined();
        $encoder->shouldReceive('encodeErrors')->once()->withAnyArgs()->andReturnUndefined();

        /** @var SapiInterface $sapi*/

        $handler->handleException($exception, $sapi, $container);
    }

    /**
     * JSON API exception test.
     */
    public function testJsonApiHandler2()
    {
        $errors    = new ErrorCollection();
        $exception = new JsonApiException($errors);
        $handler   = new JsonApiHandler();
        $sapi      = Mockery::mock(SapiInterface::class);
        $encoder   = Mockery::mock(EncoderInterface::class);
        $container = new Container();
        $container[EncoderInterface::class]     = $encoder;
        $container[CorsStorageInterface::class] = new CorsStorage();

        /** @var Mock $sapi */
        /** @var Mock $encoder */

        $sapi->shouldReceive('handleResponse')->once()->withAnyArgs()->andReturnUndefined();
        $encoder->shouldReceive('encodeErrors')->once()->withAnyArgs()->andReturnUndefined();

        /** @var SapiInterface $sapi*/

        $handler->handleException($exception, $sapi, $container);
    }

    /**
     * JSON API exception test.
     */
    public function testJsonApiHandler3()
    {
        $errors                                 = new ErrorCollection();
        $exception                              = new JsonApiException($errors);
        $handler                                = new JsonApiHandler();
        $sapi                                   = Mockery::mock(SapiInterface::class);
        $encoder                                = Mockery::mock(EncoderInterface::class);
        $container                              = new Container();
        $container[EncoderInterface::class]     = $encoder;
        $corsStorage                            = new CorsStorage();
        $container[CorsStorageInterface::class] = $corsStorage;

        $corsStorage->setHeaders(['foo' => 'boo']);

        /** @var Mock $sapi */
        /** @var Mock $encoder */

        $sapi->shouldReceive('handleResponse')->once()->withAnyArgs()->andReturnUndefined();
        $encoder->shouldReceive('encodeErrors')->once()->withAnyArgs()->andReturnUndefined();

        /** @var SapiInterface $sapi*/

        $handler->handleException($exception, $sapi, $container);
    }

    /**
     * Non JSON API exception test.
     */
    public function testJsonApiHandlerWithNonJsonApiException()
    {
        $exception                              = new LogicException();
        $handler                                = new JsonApiHandler();
        $sapi                                   = Mockery::mock(SapiInterface::class);
        $encoder                                = Mockery::mock(EncoderInterface::class);
        $config                                 = Mockery::mock(ConfigInterface::class);
        $container                              = new Container();
        $container[EncoderInterface::class]     = $encoder;
        $corsStorage                            = new CorsStorage();
        $container[CorsStorageInterface::class] = $corsStorage;
        $container[ConfigInterface::class]      = $config;

        $corsStorage->setHeaders(['foo' => 'boo']);

        /** @var Mock $sapi */
        /** @var Mock $config */
        /** @var Mock $encoder */

        $sapi->shouldReceive('handleResponse')->once()->withAnyArgs()->andReturnUndefined();
        $config->shouldReceive('getConfig')->once()->withAnyArgs()->andReturn([
            Application::KEY_IS_DEBUG => true
        ]);
        $encoder->shouldReceive('encodeErrors')->once()->withAnyArgs()->andReturnUndefined();

        /** @var SapiInterface $sapi*/

        $handler->handleException($exception, $sapi, $container);
    }
}
