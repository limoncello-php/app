<?php namespace App\Container;

use App\Api\Factories\JsonApiFactory;
use App\Commands\CacheModelSchemes;
use App\Exceptions\JsonApiHandler;
use Config\ConfigInterface;
use Config\Services\JsonApi\JsonApiConfigInterface as C;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Application\ExceptionHandlerInterface;
use Limoncello\JsonApi\Contracts\Encoder\EncoderInterface;
use Limoncello\JsonApi\Contracts\FactoryInterface;
use Limoncello\JsonApi\Contracts\Schema\ContainerInterface as SchemesContainerInterface;
use Limoncello\Models\Contracts\SchemaStorageInterface;
use Limoncello\Models\SchemaStorage;
use Neomerx\JsonApi\Contracts\Http\Query\QueryParametersParserInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;

/**
 * @package App
 */
trait SetUpJsonApi
{
    /**
     * @param Container $container
     *
     * @return void
     */
    protected static function setUpJsonApi(Container $container)
    {
        $factory = new JsonApiFactory();

        $container[FactoryInterface::class] = function () use ($factory) {
            return $factory;
        };

        $container[QueryParametersParserInterface::class] = function () use ($factory) {
            return $factory->getJsonApiFactory()->createQueryParametersParser();
        };

        $container[SchemaStorageInterface::class] = function (ContainerInterface $container) use ($factory) {
            /** @var ConfigInterface $config */
            $config        = $container->get(ConfigInterface::class);
            $modelSchemes  = new SchemaStorage();

            $cachedRoutes = '\\' . CacheModelSchemes::CACHED_NAMESPACE . '\\' .
                CacheModelSchemes::CACHED_CLASS . '::' . CacheModelSchemes::CACHED_METHOD;
            if ($config->useAppCache() === true && is_callable($cachedRoutes) === true) {
                $schemesData = call_user_func($cachedRoutes);
                $modelSchemes->setData($schemesData);
            } else {
                $jsonApiConfig = $config->getConfig()[ConfigInterface::KEY_JSON_API];
                $modelClasses  = array_keys($jsonApiConfig[C::KEY_MODEL_TO_SCHEMA_MAP]);
                CacheModelSchemes::buildModelSchemes($modelSchemes, $modelClasses);
            }

            return $modelSchemes;
        };

        $container[SchemesContainerInterface::class] = function (ContainerInterface $container) use ($factory) {
            /** @var SchemaStorageInterface $modelSchemes */
            $modelSchemes = $container->get(SchemaStorageInterface::class);

            /** @var ConfigInterface $config */
            $config        = $container->get(ConfigInterface::class);
            $jsonApiConfig = $config->getConfig()[ConfigInterface::KEY_JSON_API];

            $jsonSchemesMap = $jsonApiConfig[C::KEY_MODEL_TO_SCHEMA_MAP];
            $jsonSchemes    = $factory->createContainer($jsonSchemesMap, $modelSchemes);

            return $jsonSchemes;
        };

        $container[EncoderInterface::class] = function (ContainerInterface $container) use ($factory) {
            /** @var FactoryInterface $factory */
            $factory = $container->get(FactoryInterface::class);

            /** @var ConfigInterface $config */
            $config        = $container->get(ConfigInterface::class);
            $jsonApiConfig = $config->getConfig()[ConfigInterface::KEY_JSON_API];
            $encoderConfig = $jsonApiConfig[C::KEY_JSON];

            /** @var SchemesContainerInterface $jsonSchemes */
            $jsonSchemes = $container->get(SchemesContainerInterface::class);

            $urlPrefix = $encoderConfig[C::KEY_JSON_URL_PREFIX];
            $encoder   = $factory->createEncoder($jsonSchemes, new EncoderOptions(
                $encoderConfig[C::KEY_JSON_OPTIONS],
                $urlPrefix,
                $encoderConfig[C::KEY_JSON_DEPTH]
            ));
            if (isset($encoderConfig[C::KEY_JSON_VERSION_META]) === true) {
                $meta = $encoderConfig[C::KEY_JSON_VERSION_META];
                $encoder->withMeta($meta);
            }
            if (isset($encoderConfig[C::KEY_JSON_IS_SHOW_VERSION]) === true &&
                $encoderConfig[C::KEY_JSON_IS_SHOW_VERSION] === true
            ) {
                $encoder->withJsonApiVersion();
            }

            return $encoder;
        };

        $container[ExceptionHandlerInterface::class] = function () {
            return new JsonApiHandler();
        };
    }
}
