<?php namespace App\Json\Controllers;

use App\Api\UsersApi as Api;
use App\Json\Schemes\UserSchema as Schema;
use App\Validation\User\UserCreateJson;
use App\Validation\User\UsersReadQuery;
use App\Validation\User\UserUpdateJson;

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
    const ON_CREATE_DATA_VALIDATION_RULES_CLASS = UserCreateJson::class;

    /** @inheritdoc */
    const ON_UPDATE_DATA_VALIDATION_RULES_CLASS = UserUpdateJson::class;

    /** @inheritdoc */
    const ON_INDEX_QUERY_VALIDATION_RULES_CLASS = UsersReadQuery::class;

    /** @inheritdoc */
    const ON_READ_QUERY_VALIDATION_RULES_CLASS = UsersReadQuery::class;
}
