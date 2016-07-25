<?php namespace App\Http\Controllers;

use App\Api\PostsApi as Api;
use App\Api\Validation\Validator;
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
        /** @var Validator $validator */
        $validator = $container->get(Validator::class);
        $json      = static::parseJson($container, $request);
        $schema    = static::getSchema($container);

        $idRule         = $validator->absentOrNull();
        $attributeRules = [
            Schema::ATTR_TITLE => $validator->requiredPostTitle(),
            Schema::ATTR_TEXT  => $validator->requiredPostText(),
        ];
        $toOneRules     = [
            Schema::REL_BOARD => $validator->requiredBoardId(),
        ];

        list (, $attrCaptures, $toManyCaptures) =
            $validator->assert($schema, $json, $idRule, $attributeRules, $toOneRules);

        return [$attrCaptures, $toManyCaptures];
    }

    /**
     * @inheritdoc
     */
    public static function parseInputOnUpdate(
        $index,
        ContainerInterface $container,
        ServerRequestInterface $request
    ) {
        /** @var Validator $validator */
        $validator = $container->get(Validator::class);
        $json      = static::parseJson($container, $request);
        $schema    = static::getSchema($container);

        $idRule         = $validator->idEquals($index);
        $attributeRules = [
            Schema::ATTR_TEXT => $validator->optionalPostText(),
        ];

        list (, $attrCaptures, $toManyCaptures) = $validator->assert($schema, $json, $idRule, $attributeRules);

        return [$attrCaptures, $toManyCaptures];
    }
}
