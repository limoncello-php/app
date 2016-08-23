<?php namespace App\Http\Controllers;

use App\Api\RolesApi as Api;
use App\Http\Validators\RolesValidator as Validator;
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

    /** @inheritdoc */
    const VALIDATOR_CLASS = Validator::class;

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
