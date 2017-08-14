<?php namespace App\Http\Controllers;

use App\Json\Api\CommentsApi as Api;
use App\Json\Schemes\CommentScheme as Scheme;
use App\Json\Validators\CommentCreate;
use App\Json\Validators\CommentUpdate;
use Limoncello\Flute\Http\BaseController;

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
    const SCHEMA_CLASS = Scheme::class;

    /** @inheritdoc */
    const ON_CREATE_VALIDATION_RULES_SET_CLASS = CommentCreate::class;

    /** @inheritdoc */
    const ON_UPDATE_VALIDATION_RULES_SET_CLASS = CommentUpdate::class;
}
