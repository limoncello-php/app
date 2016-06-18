<?php namespace App\Api;

use App\Api\Contracts\UsersApiInterface;
use App\Database\Models\User as Model;

/**
 * @package App
 */
class UsersApi extends BaseApi implements UsersApiInterface
{
    const MODEL = Model::class;
}
