<?php namespace App\Http\Controllers\Posts;

use App\Api\PostsApi as Api;
use App\Http\Controllers\BaseController;
use App\Schemes\PostSchema as Schema;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 */
class Controller extends BaseController
{
    /** @inheritdoc */
    const API_CLASS = Api::class;

    const SCHEMA_CLASS = Schema::class;

    /** @inheritdoc */
    const CREATE_TRANSFORMER_CLASS = OnCreate::class;

    /** @inheritdoc */
    const UPDATE_TRANSFORMER_CLASS = OnUpdate::class;

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
}
