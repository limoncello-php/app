<?php namespace App\Web\Controllers;

use App\Web\L10n\Views;
use Limoncello\Contracts\Application\ApplicationConfigurationInterface as A;
use Limoncello\Contracts\Application\CacheSettingsProviderInterface;
use Limoncello\Contracts\L10n\FormatterFactoryInterface;
use Limoncello\Contracts\Templates\TemplatesInterface;
use Limoncello\Flute\Contracts\Api\CrudInterface;
use Limoncello\Flute\Contracts\FactoryInterface;
use Limoncello\Flute\Contracts\Http\Query\ParametersMapperInterface;
use Limoncello\Flute\Contracts\Http\Query\QueryParserInterface;
use Limoncello\Flute\Contracts\Http\Query\QueryValidatorFactoryInterface;
use Limoncello\Flute\Contracts\Http\Query\QueryValidatorInterface;
use Limoncello\Flute\Contracts\Validation\FormValidatorFactoryInterface;
use Limoncello\Flute\Contracts\Validation\FormValidatorInterface;
use Limoncello\Templates\TwigTemplates;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Twig_Extensions_Extension_Text;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
abstract class BaseController
{
    /**
     * @param PsrContainerInterface $container
     * @param int                   $viewId
     * @param array                 $parameters
     * @param string                $viewsNamespace
     *
     * @return string
     */
    protected static function view(
        PsrContainerInterface $container,
        int $viewId,
        array $parameters = [],
        string $viewsNamespace = Views::NAMESPACE
    ): string {
        /** @var FormatterFactoryInterface $factory */
        $factory      = $container->get(FormatterFactoryInterface::class);
        $formatter    = $factory->createFormatter($viewsNamespace);
        $templateName = $formatter->formatMessage($viewId);

        /** @var TemplatesInterface $templates */
        $templates = $container->get(TemplatesInterface::class);
        if ($templates instanceof TwigTemplates) {
            $templates->getTwig()->addExtension(new Twig_Extensions_Extension_Text());
        }

        /** @var CacheSettingsProviderInterface $provider */
        $provider  = $container->get(CacheSettingsProviderInterface::class);
        $originUri = $provider->getApplicationConfiguration()[A::KEY_APP_ORIGIN_URI];

        $defaultParams = [
            '_origin_uri' => $originUri,
        ];

        $body = $templates->render($templateName, $parameters + $defaultParams);

        return $body;
    }

    /**
     * @param PsrContainerInterface $container
     * @param string                $formValidatorClass
     *
     * @return FormValidatorInterface
     */
    protected static function validator(
        PsrContainerInterface $container,
        string $formValidatorClass
    ): FormValidatorInterface {
        /** @var FormValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $container->get(FormValidatorFactoryInterface::class);
        $validator        = $validatorFactory->createValidator($formValidatorClass);

        return $validator;
    }

    /**
     * @param PsrContainerInterface $container
     * @param array                 $queryParameters
     *
     * @return QueryParserInterface
     */
    protected static function createQueryParser(
        PsrContainerInterface $container,
        array $queryParameters
    ): QueryParserInterface {
        /** @var QueryParserInterface $queryParser */
        $queryParser = $container->get(QueryParserInterface::class);

        return $queryParser->parse($queryParameters);
    }

    /**
     * @param PsrContainerInterface $container
     * @param string                $className
     * @param array                 $queryParameters
     *
     * @return QueryValidatorInterface
     */
    protected static function createQueryValidator(
        PsrContainerInterface $container,
        string $className,
        array $queryParameters
    ): QueryValidatorInterface {
        /** @var QueryValidatorFactoryInterface $factory */
        $factory   = $container->get(QueryValidatorFactoryInterface::class);
        $validator = $factory->createValidator($className);
        $validator->parse($queryParameters);

        return $validator;
    }

    /**
     * @param PsrContainerInterface $container
     * @param string                $apiClass
     *
     * @return CrudInterface
     */
    protected static function createApi(PsrContainerInterface $container, string $apiClass): CrudInterface
    {
        /** @var FactoryInterface $factory */
        $factory = $container->get(FactoryInterface::class);
        $api     = $factory->createApi($apiClass);

        return $api;
    }

    /**
     * @param PsrContainerInterface $container
     * @param string                $jsonType
     *
     * @return ParametersMapperInterface
     */
    protected static function createParameterMapper(
        PsrContainerInterface $container,
        string $jsonType
    ): ParametersMapperInterface {
        /** @var ParametersMapperInterface $mapper */
        $mapper = $container->get(ParametersMapperInterface::class);
        $mapper->selectRootSchemeByResourceType($jsonType);

        return $mapper;
    }
}
