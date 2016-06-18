<?php namespace App\Api;

use App\Api\Contracts\BoardsApiInterface;
use App\Database\Models\Board as Model;

/**
 * @package App
 */
class BoardsApi extends BaseApi implements BoardsApiInterface
{
    const MODEL = Model::class;
}
