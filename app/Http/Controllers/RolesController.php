<?php namespace App\Http\Controllers;

use App\Json\Api\RolesApi as Api;
use App\Json\Schemes\RoleScheme as Scheme;
use App\Json\Validators\RoleCreate;
use App\Json\Validators\RoleUpdate;
use Limoncello\Flute\Http\BaseController;

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
    const SCHEMA_CLASS = Scheme::class;

    /** @inheritdoc */
    const ON_CREATE_VALIDATION_RULES_SET_CLASS = RoleCreate::class;

    /** @inheritdoc */
    const ON_UPDATE_VALIDATION_RULES_SET_CLASS = RoleUpdate::class;
}
