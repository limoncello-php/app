<?php namespace App\Http\Controllers;

use App\Api\UsersApi as Api;
use App\Api\Validation\Validator;
use App\Schemes\UserSchema as Schema;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * @package App
 */
class UsersController extends BaseController
{
    /** @inheritdoc */
    const API_CLASS = Api::class;

    const SCHEMA_CLASS = Schema::class;

    /** Form key */
    const FORM_EMAIL = 'email';

    /** Form key */
    const FORM_PASSWORD = 'password';

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public static function readRole(array $routeParams, ContainerInterface $container, ServerRequestInterface $request)
    {
        $index    = $routeParams[static::ROUTE_KEY_INDEX];
        $response = static::readRelationship($index, Schema::REL_ROLE, $container, $request);

        return $response;
    }

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public static function readPosts(array $routeParams, ContainerInterface $container, ServerRequestInterface $request)
    {
        $index    = $routeParams[static::ROUTE_KEY_INDEX];
        $response = static::readRelationship($index, Schema::REL_POSTS, $container, $request);

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
            Schema::ATTR_TITLE      => $validator->requiredUserTitle(),
            Schema::ATTR_FIRST_NAME => $validator->requiredUserFirstName(),
            Schema::ATTR_LAST_NAME  => $validator->requiredUserLastName(),
            Schema::ATTR_LANGUAGE   => $validator->requiredUserLanguage(),
            Schema::ATTR_EMAIL      => $validator->requiredUserEmail(),
            Schema::V_ATTR_PASSWORD => $validator->requiredUserPassword(),
        ];
        $toOneRules     = [
            Schema::REL_ROLE => $validator->requiredRoleId(),
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
            Schema::ATTR_TITLE      => $validator->optionalUserTitle(),
            Schema::ATTR_FIRST_NAME => $validator->optionalUserFirstName(),
            Schema::ATTR_LAST_NAME  => $validator->optionalUserLastName(),
            Schema::ATTR_LANGUAGE   => $validator->optionalUserLanguage(),
            Schema::ATTR_EMAIL      => $validator->optionalUserEmail(),
            Schema::V_ATTR_PASSWORD => $validator->optionalUserPassword(),
        ];
        $toOneRules     = [
            Schema::REL_ROLE => $validator->optionalRoleId(),
        ];

        list (, $attrCaptures, $toManyCaptures) =
            $validator->assert($schema, $json, $idRule, $attributeRules, $toOneRules);

        return [$attrCaptures, $toManyCaptures];
    }

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public static function authenticate(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ) {
        // suppress unused variable
        $routeParams ?: null;

        $formData = $request->getParsedBody();
        if (is_array($formData) === false ||
            array_key_exists(self::FORM_EMAIL, $formData) === false ||
            array_key_exists(self::FORM_PASSWORD, $formData) === false
        ) {
            return new EmptyResponse(400);
        }

        $email    = $formData[self::FORM_EMAIL];
        $password = $formData[self::FORM_PASSWORD];
        if (is_string($email) === false || is_string($password) === false) {
            return new EmptyResponse(400);
        }

        /** @var Api $api */
        $api   = static::createApi($container);
        $token = $api->authenticate($email, $password);
        if ($token === null) {
            return new EmptyResponse(401);
        }

        return new JsonResponse($token);
    }
}
