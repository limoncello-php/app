<?php namespace App\Json\Controllers;

use App\Api\RolesApi as Api;
use App\Data\Models\Role as Model;
use App\Json\Schemes\RoleSchema as Schema;
use App\Validation\Role\RoleCreateJson;
use App\Validation\Role\RolesReadQuery;
use App\Validation\Role\RoleUpdateJson;
use Limoncello\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
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
class RolesController extends BaseController
{
    /** @inheritdoc */
    const API_CLASS = Api::class;

    /** @inheritdoc */
    const SCHEMA_CLASS = Schema::class;

    /** @inheritdoc */
    const ON_CREATE_DATA_VALIDATION_RULES_CLASS = RoleCreateJson::class;

    /** @inheritdoc */
    const ON_UPDATE_DATA_VALIDATION_RULES_CLASS = RoleUpdateJson::class;

    /** @inheritdoc */
    const ON_INDEX_QUERY_VALIDATION_RULES_CLASS = RolesReadQuery::class;

    /** @inheritdoc */
    const ON_READ_QUERY_VALIDATION_RULES_CLASS = RolesReadQuery::class;

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
    public static function readUsers(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        return static::readRelationship(
            $routeParams[static::ROUTE_KEY_INDEX],
            Model::REL_USERS,
            DefaultQueryValidationRules::class,
            $container,
            $request
        );
    }
}
