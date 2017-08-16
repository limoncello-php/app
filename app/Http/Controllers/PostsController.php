<?php namespace App\Http\Controllers;

use App\Json\Api\PostsApi as Api;
use App\Json\Schemes\PostScheme as Scheme;
use App\Json\Validators\PostCreate;
use App\Json\Validators\PostUpdate;
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
        $index    = $routeParams[static::ROUTE_KEY_INDEX];
        $response = static::readRelationship($index, Scheme::REL_COMMENTS, $container, $request);

        return $response;
    }
}
