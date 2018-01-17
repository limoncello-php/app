<?php namespace App\Json\Controllers;

use App\Api\BoardsApi as Api;
use App\Data\Models\Board as Model;
use App\Json\Schemes\BoardScheme as Scheme;
use App\Validation\JsonValidators\Board\BoardCreate;
use App\Validation\JsonValidators\Board\BoardUpdate;
use Limoncello\Flute\Contracts\Http\Query\QueryParserInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class BoardsController extends BaseController
{
    /** @inheritdoc */
    const API_CLASS = Api::class;

    /** @inheritdoc */
    const SCHEMA_CLASS = Scheme::class;

    /** @inheritdoc */
    const ON_CREATE_VALIDATION_RULES_SET_CLASS = BoardCreate::class;

    /** @inheritdoc */
    const ON_UPDATE_VALIDATION_RULES_SET_CLASS = BoardUpdate::class;

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function readPosts(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        return static::readRelationship(
            $routeParams[static::ROUTE_KEY_INDEX],
            Model::REL_POSTS,
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
                Scheme::REL_POSTS,
            ]);
    }
}
