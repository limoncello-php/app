<?php namespace App\Http\Controllers;

use App\Api\RolesApi as Api;
use App\Api\Validation\Validator;
use App\Schemes\RoleSchema as Schema;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 */
class RolesController extends BaseController
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
    public static function readUsers(array $routeParams, ContainerInterface $container, ServerRequestInterface $request)
    {
        $index    = $routeParams[static::ROUTE_KEY_INDEX];
        $response = static::readRelationship($index, Schema::REL_USERS, $container, $request);

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
            Schema::ATTR_NAME => $validator->requiredRoleName(),
        ];

        list (, $attrCaptures, $toManyCaptures) = $validator->assert($schema, $json, $idRule, $attributeRules);

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
            Schema::ATTR_NAME => $validator->requiredRoleName(),
        ];

        list (, $attrCaptures, $toManyCaptures) = $validator->assert($schema, $json, $idRule, $attributeRules);

        return [$attrCaptures, $toManyCaptures];
    }
}
