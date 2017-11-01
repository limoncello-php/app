<?php namespace App\Json\Controllers;

use App\Api\CommentsApi as Api;
use App\Json\Schemes\CommentScheme as Scheme;
use App\Json\Validators\Comment\CommentCreate;
use App\Json\Validators\Comment\CommentUpdate;
use Limoncello\Flute\Contracts\Http\Query\QueryParserInterface;

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

    /**
     * @inheritdoc
     *
     * By default no filters, sorts and includes are allowed (will be ignored). We override this method
     * in order allow it.
     */
    protected static function configureOnIndexParser(QueryParserInterface $parser): QueryParserInterface
    {
        return parent::configureOnIndexParser($parser)
            ->withAllowedFilterFields([
                Scheme::RESOURCE_ID,
                Scheme::REL_POST,
                Scheme::REL_USER,
            ])
            ->withAllowedSortFields([
                Scheme::RESOURCE_ID,
                Scheme::ATTR_TEXT,
            ]);
    }
}
