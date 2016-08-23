<?php namespace App\Http\Controllers;

use App\Container\SetUpAuth;
use App\Container\SetUpCrypt;
use App\Container\SetUpJsonApi;
use App\Http\Validators\BaseValidator;
use App\Schemes\BaseSchema;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 */
abstract class BaseController extends \Limoncello\JsonApi\Http\BaseController
{
    use SetUpAuth, SetUpCrypt, SetUpJsonApi;

    /** API URI prefix */
    const API_URI_PREFIX = '/api/v1';

    /** URI key used in routing table */
    const ROUTE_KEY_INDEX = 'idx';

    /** JSON API Validator class name */
    const VALIDATOR_CLASS = null;

    /**
     * @param Container $container
     *
     * @return void
     */
    public static function containerConfigurator(Container $container)
    {
        self::setUpAuth($container);
        self::setUpCrypt($container);
        self::setUpJsonApi($container);
    }

    /**
     * @param string             $class
     * @param ContainerInterface $container
     *
     * @return BaseValidator
     */
    protected static function createValidatorFromClass($class, ContainerInterface $container)
    {
        return new $class($container);
    }

    /**
     * @inheritdoc
     */
    protected static function parseJson(ContainerInterface $container, ServerRequestInterface $request)
    {
        $json = parent::parseJson($container, $request);

        // The client tends to send a few read-only attributes back to server.
        // In order to avoid validation errors we'll delete such attributes
        unset(
            $json[DocumentInterface::KEYWORD_DATA][DocumentInterface::KEYWORD_ATTRIBUTES][BaseSchema::ATTR_CREATED_AT]
        );
        unset(
            $json[DocumentInterface::KEYWORD_DATA][DocumentInterface::KEYWORD_ATTRIBUTES][BaseSchema::ATTR_UPDATED_AT]
        );

        return $json;
    }
}
