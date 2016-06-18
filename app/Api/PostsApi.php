<?php namespace App\Api;

use App\Api\Contracts\PostsApiInterface;
use App\Database\Models\Post as Model;

/**
 * @package App
 */
class PostsApi extends BaseApi implements PostsApiInterface
{
    const MODEL = Model::class;
}
