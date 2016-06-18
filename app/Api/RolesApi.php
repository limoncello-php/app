<?php namespace App\Api;

use App\Api\Contracts\RolesApiInterface;
use App\Database\Models\Role as Model;

/**
 * @package App
 */
class RolesApi extends BaseApi implements RolesApiInterface
{
    const MODEL = Model::class;
}
