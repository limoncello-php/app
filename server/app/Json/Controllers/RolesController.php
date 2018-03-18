<?php namespace App\Json\Controllers;

use App\Api\RolesApi as Api;
use App\Json\Schemes\RoleSchema as Schema;
use App\Validation\Role\RoleCreateJson;
use App\Validation\Role\RolesReadQuery;
use App\Validation\Role\RoleUpdateJson;

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
}
