<?php namespace App\Container;

use App\Api\Factories\JsonApiFactory;
use App\Exceptions\JsonApiHandler;
use App\Http\Pagination\PaginationStrategy;
use Config\ConfigInterface;
use Doctrine\DBAL\Connection;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Application\ExceptionHandlerInterface;
use Limoncello\JsonApi\Adapters\FilterOperations;
use Limoncello\JsonApi\Config\JsonApiConfig;
use Limoncello\JsonApi\Contracts\Adapters\PaginationStrategyInterface;
use Limoncello\JsonApi\Contracts\Adapters\RepositoryInterface;
use Limoncello\JsonApi\Contracts\Config\JsonApiConfigInterface;
use Limoncello\JsonApi\Contracts\Encoder\EncoderInterface;
use Limoncello\JsonApi\Contracts\FactoryInterface;
use Limoncello\JsonApi\Contracts\Models\ModelSchemesInterface;
use Limoncello\JsonApi\Contracts\Schema\JsonSchemesInterface;
use Neomerx\JsonApi\Contracts\Http\Query\QueryParametersParserInterface;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Limoncello\JsonApi\Contracts\I18n\TranslatorInterface;
use Limoncello\Validation\Contracts\TranslatorInterface as ValidationTranslatorInterface;
use Limoncello\Validation\I18n\Translator as ValidationTranslator;
use App\I18n\En\Validation;

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

        $container[ValidationTranslatorInterface::class] = function (/*ContainerInterface $container*/) {
            // TODO load locale according to current user preferences
            return new ValidationTranslator(Validation::getLocaleCode(), Validation::getMessages());
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
