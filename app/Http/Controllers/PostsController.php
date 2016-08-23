<?php namespace App\Http\Controllers;

use App\Api\PostsApi as Api;
use App\Http\Validators\PostsValidator as Validator;
use App\Schemes\PostSchema as Schema;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 */
class PostsController extends BaseController
{
    /** @inheritdoc */
    const API_CLASS = Api::class;

    const SCHEMA_CLASS = Schema::class;

    /** @inheritdoc */
    const VALIDATOR_CLASS = Validator::class;

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public static function readBoard(array $routeParams, ContainerInterface $container, ServerRequestInterface $request)
    {
        $index    = $routeParams[static::ROUTE_KEY_INDEX];
        $response = static::readRelationship($index, Schema::REL_BOARD, $container, $request);

        return $response;
    }

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public static function readUser(array $routeParams, ContainerInterface $container, ServerRequestInterface $request)
    {
        $index    = $routeParams[static::ROUTE_KEY_INDEX];
        $response = static::readRelationship($index, Schema::REL_USER, $container, $request);

        return $response;
    }

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public static function readComments(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ) {
        $index    = $routeParams[static::ROUTE_KEY_INDEX];
        $response = static::readRelationship($index, Schema::REL_COMMENTS, $container, $request);

        return $response;
    }


    /**
     * @inheritdoc
     */
    public static function parseInputOnCreate(
        ContainerInterface $container,
        ServerRequestInterface $request
    ) {
        $validator = static::getValidator($container);
        $json      = static::parseJson($container, $request);
        $captures  = $validator->parseAndValidateOnCreate($json);

        return $captures;
    }

    /**
     * @inheritdoc
     */
    public static function parseInputOnUpdate(
        $index,
        ContainerInterface $container,
        ServerRequestInterface $request
    ) {
        $validator = static::getValidator($container);
        $json      = static::parseJson($container, $request);
        $captures  = $validator->parseAndValidateOnUpdate($index, $json);

        return $captures;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return Validator
     */
    protected static function getValidator(ContainerInterface $container)
    {
        return static::createValidatorFromClass(static::VALIDATOR_CLASS, $container);
    }
}
