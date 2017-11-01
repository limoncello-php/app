<?php namespace App\Http\Controllers\Web;

use App\Http\L10n\Views;
use Limoncello\Contracts\L10n\FormatterFactoryInterface;
use Limoncello\Contracts\Templates\TemplatesInterface;
use Limoncello\Flute\Contracts\Api\CrudInterface;
use Limoncello\Flute\Contracts\FactoryInterface;
use Limoncello\Flute\Contracts\Http\Query\QueryParserInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Settings\Application;

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

        $defaultParams = [
            '_origin_uri' => Application::ORIGIN_URI,
        ];

        $body = $templates->render($templateName, $parameters + $defaultParams);

        return $body;
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
}
