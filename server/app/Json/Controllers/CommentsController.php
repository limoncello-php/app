<?php namespace App\Json\Controllers;

use App\Api\CommentsApi as Api;
use App\Json\Schemes\CommentSchema as Schema;
use App\Validation\Comment\CommentCreateJson;
use App\Validation\Comment\CommentsReadQuery;
use App\Validation\Comment\CommentUpdateJson;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class CommentsController extends BaseController
{
    /** @inheritdoc */
    const API_CLASS = Api::class;

    /** @inheritdoc */
    const SCHEMA_CLASS = Schema::class;

    /** @inheritdoc */
    const ON_CREATE_DATA_VALIDATION_RULES_CLASS = CommentCreateJson::class;

    /** @inheritdoc */
    const ON_UPDATE_DATA_VALIDATION_RULES_CLASS = CommentUpdateJson::class;

    /** @inheritdoc */
    const ON_INDEX_QUERY_VALIDATION_RULES_CLASS = CommentsReadQuery::class;

    /** @inheritdoc */
    const ON_READ_QUERY_VALIDATION_RULES_CLASS = CommentsReadQuery::class;
}
