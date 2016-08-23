<?php namespace App\Http\Controllers;

use App\Api\UsersApi as Api;
use App\Http\Validators\UsersValidator as Validator;
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

    /** @inheritdoc */
    const VALIDATOR_CLASS = Validator::class;

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

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public static function readPosts(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ) {
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
        $validator = static::getValidator($container);
        $json      = static::parseJson($container, $request);
        $captures  = $validator->parseAndValidateUserOnCreate($json);

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
        $captures  = $validator->parseAndValidateUserOnUpdate($index, $json);

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
