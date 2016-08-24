<?php namespace App\Api;

use App\Api\Traits\SoftDeletes;
use App\Database\Models\Board as Model;

/**
 * @package App
 */
class BoardsApi extends BaseApi
{
    use SoftDeletes;

    const MODEL = Model::class;
}
