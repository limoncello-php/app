<?php namespace App\Json\Controllers;

use App\Api\UsersApi as Api;
use App\Data\Models\User as Model;
use App\Json\Schemes\UserSchema as Schema;
use App\Validation\JsonValidators\User\UserCreate;
use App\Validation\JsonValidators\User\UserUpdate;
use Limoncello\Flute\Contracts\Http\Query\QueryParserInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class UsersController extends BaseController
{
    /** @inheritdoc */
    const API_CLASS = Api::class;

    /** @inheritdoc */
    const SCHEMA_CLASS = Schema::class;

    /** @inheritdoc */
    const ON_CREATE_VALIDATION_RULES_SET_CLASS = UserCreate::class;

    /** @inheritdoc */
    const ON_UPDATE_VALIDATION_RULES_SET_CLASS = UserUpdate::class;

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function readPosts(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        $apiHandler = function (Api $api) use ($routeParams) {
            return $api->readPosts($routeParams[static::ROUTE_KEY_INDEX]);
        };

        return static::readRelationshipWithClosure($apiHandler, Model::REL_POSTS, $container, $request);
    }

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function readComments(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        $apiHandler = function (Api $api) use ($routeParams) {
            return $api->readComments($routeParams[static::ROUTE_KEY_INDEX]);
        };

        return static::readRelationshipWithClosure($apiHandler, Model::REL_COMMENTS, $container, $request);
    }

    /**
     * @inheritdoc
     *
     * By default no filters, sorts and includes are allowed (will be ignored). We override this method
     * in order allow it.
     */
    protected static function configureOnIndexParser(QueryParserInterface $parser): QueryParserInterface
    {
        return parent::configureOnIndexParser($parser)
            ->withAllowedFilterFields([
                Schema::RESOURCE_ID,
                Schema::ATTR_FIRST_NAME,
                Schema::ATTR_LAST_NAME,
            ])
            ->withAllowedSortFields([
                Schema::RESOURCE_ID,
                Schema::ATTR_FIRST_NAME,
                Schema::ATTR_LAST_NAME,
            ])
            ->withAllowedIncludePaths([
                Schema::REL_COMMENTS,
                Schema::REL_POSTS,
            ]);
    }
}
