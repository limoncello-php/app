<?php namespace App\Http\Controllers;

use App\Container\SetUpConfig;
use App\Container\SetUpJsonApi;
use App\Container\SetUpLogs;
use App\Container\SetUpPdo;
use Config\ConfigInterface;
use Config\Services\JsonApi\JsonApiConfigInterface as C;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use Limoncello\JsonApi\Contracts\Encoder\EncoderInterface;
use Limoncello\JsonApi\Contracts\Schema\ContainerInterface as SchemesContainerInterface;
use Limoncello\JsonApi\Http\Responses;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Http\Headers\MediaType;
use Neomerx\JsonApi\Http\Headers\SupportedExtensions;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 */
abstract class BaseController extends \Limoncello\JsonApi\Http\BaseController
{
    use SetUpConfig, SetUpJsonApi, SetUpLogs, SetUpPdo;

    /** API URI prefix */
    const API_URI_PREFIX = '/api/v1';

    /** URI key used in routing table */
    const ROUTE_KEY_INDEX = 'idx';

    /**
     * @param Container $container
     *
     * @return void
     */
    public static function containerConfigurator(Container $container)
    {
        self::setUpPdo($container);
        self::setUpJsonApi($container);
    }

    /**
     * @inheritdoc
     */
    public static function createResponses(
        ContainerInterface $container,
        ServerRequestInterface $request,
        EncodingParametersInterface $parameters = null
    ) {
        /** @var EncoderInterface $encoder */
        $encoder = $container->get(EncoderInterface::class);
        $encoder->forOriginalUri($request->getUri());

        /** @var ConfigInterface $config */
        $config    = $container->get(ConfigInterface::class);
        $urlPrefix = $config->getConfig()[ConfigInterface::KEY_JSON_API][C::KEY_JSON][C::KEY_JSON_URL_PREFIX];

        /** @var SchemesContainerInterface $jsonSchemes */
        $jsonSchemes = $container->get(SchemesContainerInterface::class);
        $responses   = new Responses(
            new MediaType(MediaTypeInterface::JSON_API_TYPE, MediaTypeInterface::JSON_API_SUB_TYPE),
            new SupportedExtensions(),
            $encoder,
            $jsonSchemes,
            $parameters,
            $urlPrefix
        );

        return $responses;
    }
}
