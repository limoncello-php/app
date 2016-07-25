<?php namespace App\Api;

use App\Database\Models\Comment as Model;

/**
 * @package App
 */
class CommentsApi extends BaseApi
{
    const MODEL = Model::class;

    /**
     * @inheritdoc
     */
    public function create(array $attributes, array $toMany = [])
    {
        // suppose we want to create comments using current user as an author.
        $curUserId = 1;

        $attributes[Model::FIELD_ID_USER] = $curUserId;

        return parent::create($attributes, $toMany);
    }
}
