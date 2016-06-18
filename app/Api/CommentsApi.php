<?php namespace App\Api;

use App\Api\Contracts\CommentsApiInterface;
use App\Database\Models\Comment as Model;

/**
 * @package App
 */
class CommentsApi extends BaseApi implements CommentsApiInterface
{
    const MODEL = Model::class;
}
