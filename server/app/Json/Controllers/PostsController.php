<?php namespace App\Json\Controllers;

use App\Api\PostsApi as Api;
use App\Json\Schemes\PostScheme as Scheme;
use App\Validation\JsonValidators\Post\PostCreate;
use App\Validation\JsonValidators\Post\PostUpdate;
use Limoncello\Flute\Contracts\Http\Query\QueryParserInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class PostsController extends BaseController
{
    /** @inheritdoc */
    const API_CLASS = Api::class;

    /** @inheritdoc */
    const SCHEMA_CLASS = Scheme::class;

    /** @inheritdoc */
    const ON_CREATE_VALIDATION_RULES_SET_CLASS = PostCreate::class;

    /** @inheritdoc */
    const ON_UPDATE_VALIDATION_RULES_SET_CLASS = PostUpdate::class;

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public static function readComments(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        return static::readRelationship(
            $routeParams[static::ROUTE_KEY_INDEX],
            Scheme::REL_COMMENTS,
            $container,
            $request
        );
    }

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
            ])
            ->withAllowedSortFields([
                Scheme::RESOURCE_ID,
                Scheme::ATTR_TITLE,
            ])
            ->withAllowedIncludePaths([
                Scheme::REL_COMMENTS,
            ]);
    }
}
