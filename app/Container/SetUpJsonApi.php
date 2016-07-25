<?php namespace App\Container;

use App\Api\Factories\JsonApiFactory;
use App\Api\Validation\Locales\EnUsLocale;
use App\Api\Validation\Validator;
use App\Commands\CacheModelSchemes;
use App\Exceptions\JsonApiHandler;
use Config\ConfigInterface;
use Doctrine\DBAL\Connection;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Application\ExceptionHandlerInterface;
use Limoncello\JsonApi\Adapters\FilterOperations;
use Limoncello\JsonApi\Adapters\PaginationStrategy;
use Limoncello\JsonApi\Config\JsonApiConfig;
use Limoncello\JsonApi\Contracts\Adapters\PaginationStrategyInterface;
use Limoncello\JsonApi\Contracts\Adapters\RepositoryInterface;
use Limoncello\JsonApi\Contracts\Config\JsonApiConfigInterface;
use Limoncello\JsonApi\Contracts\Encoder\EncoderInterface;
use Limoncello\JsonApi\Contracts\FactoryInterface;
use Limoncello\JsonApi\Contracts\Schema\JsonSchemesInterface;
use Limoncello\Models\Contracts\ModelSchemesInterface;
use Limoncello\Models\ModelSchemes;
use Neomerx\JsonApi\Contracts\Http\Query\QueryParametersParserInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Limoncello\JsonApi\Contracts\I18n\TranslatorInterface;
use Limoncello\Validation\Contracts\TranslatorInterface as ValidationTranslatorInterface;
use Limoncello\Validation\I18n\Translator as ValidationTranslator;

// TODO think about moving as much JSON API config trait to json-api lib as possible

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

        $container[ModelSchemesInterface::class] = function (ContainerInterface $container) use ($factory) {
            /** @var ConfigInterface $config */
            $config        = $container->get(ConfigInterface::class);
            $modelSchemes  = new ModelSchemes();
            $cachedRoutes  = '\\' . CacheModelSchemes::CACHED_NAMESPACE . '\\' .
                CacheModelSchemes::CACHED_CLASS . '::' . CacheModelSchemes::CACHED_METHOD;
            if ($config->useAppCache() === true && is_callable($cachedRoutes) === true) {
                $schemesData = call_user_func($cachedRoutes);
                $modelSchemes->setData($schemesData);
            } else {
                /** @var JsonApiConfigInterface $jsonApiConfig */
                $jsonApiConfig = $container->get(JsonApiConfigInterface::class);
                $modelClasses  = array_keys($jsonApiConfig->getModelSchemaMap());
                CacheModelSchemes::buildModelSchemes($modelSchemes, $modelClasses);
            }

            return $modelSchemes;
        };

        $container[JsonSchemesInterface::class] = function (ContainerInterface $container) use ($factory) {
            /** @var JsonApiConfigInterface $jsonApiConfig */
            /** @var ModelSchemesInterface $modelSchemes */
            $jsonApiConfig = $container->get(JsonApiConfigInterface::class);
            $modelSchemes  = $container->get(ModelSchemesInterface::class);

            return $factory->createJsonSchemes($jsonApiConfig->getModelSchemaMap(), $modelSchemes);
        };

        $container[EncoderInterface::class] = function (ContainerInterface $container) use ($factory) {
            /** @var JsonApiConfigInterface $jsonApiConfig */
            /** @var JsonSchemesInterface $jsonSchemes */
            $jsonApiConfig = $container->get(JsonApiConfigInterface::class);
            $jsonSchemes   = $container->get(JsonSchemesInterface::class);
            $encoder       = $factory->createEncoder($jsonSchemes, new EncoderOptions(
                $jsonApiConfig->getJsonEncodeOptions(),
                $jsonApiConfig->getUriPrefix(),
                $jsonApiConfig->getJsonEncodeDepth()
            ));
            if ($jsonApiConfig->getMeta() !== null) {
                $encoder->withMeta($jsonApiConfig->getMeta());
            }
            if ($jsonApiConfig->isShowVersion() === true) {
                $encoder->withJsonApiVersion();
            }

            return $encoder;
        };

        $container[JsonApiConfigInterface::class] = function (ContainerInterface $container) {
            /** @var ConfigInterface $appConfig */
            $appConfig  = $container->get(ConfigInterface::class);
            $configData = $appConfig->getConfig()[ConfigInterface::KEY_JSON_API];

            return (new JsonApiConfig)->setConfig($configData);
        };

        $container[TranslatorInterface::class] = $translator = $factory->createTranslator();
        $container[ValidationTranslatorInterface::class] = $validationTranslator =
            new ValidationTranslator(EnUsLocale::getLocaleCode(), EnUsLocale::getMessages());

        $container[Validator::class] = function (ContainerInterface $container) use (
            $translator,
            $validationTranslator
        ) {
            $modelSchemes = $container->get(ModelSchemesInterface::class);
            $jsonSchemes  = $container->get(JsonSchemesInterface::class);

            return new Validator(
                $translator,
                $validationTranslator,
                $jsonSchemes,
                $modelSchemes,
                $container->get(Connection::class)
            );
        };

        $container[RepositoryInterface::class] = function (ContainerInterface $container) use ($factory, $translator) {
            $connection       = $container->get(Connection::class);
            $filterOperations = new FilterOperations($translator);
            /** @var ModelSchemesInterface $modelSchemes */
            $modelSchemes     = $container->get(ModelSchemesInterface::class);

            return $factory->createRepository($connection, $modelSchemes, $filterOperations, $translator);
        };

        $container[PaginationStrategyInterface::class] = function (ContainerInterface $container) {
            /** @var JsonApiConfigInterface $jsonApiConfig */
            $jsonApiConfig = $container->get(JsonApiConfigInterface::class);

            return new PaginationStrategy($jsonApiConfig->getRelationshipPagingSize());
        };

        $container[ExceptionHandlerInterface::class] = function () {
            return new JsonApiHandler();
        };
    }
}
