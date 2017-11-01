<?php namespace App\Web\Controllers;

use App\Api\CommentsApi;
use App\Api\PostsApi;
use App\Data\Models\Comment;
use App\Data\Models\Post;
use App\Web\L10n\Views;
use Limoncello\Flute\Contracts\Http\Controller\ControllerReadInterface as CRI;
use Limoncello\Flute\Contracts\Http\ControllerInterface;
use Limoncello\Flute\Http\Query\FilterParameter;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * @package App
 */
class PostsController extends BaseController implements CRI
{
    use PaginationLinksTrait;

    /**
     * @inheritdoc
     */
    public static function read(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        $index = $routeParams[ControllerInterface::ROUTE_KEY_INDEX];

        $post = self::createApi($container, PostsApi::class)
            ->withIncludes([[Post::REL_USER]])
            ->read($index);
        // if no data return 404
        if ($post === null) {
            return new HtmlResponse(static::view($container, Views::NOT_FOUND_PAGE), 404);
        }


        // read resource with data from relationships
        $parser            = self::createQueryParser($container, $request->getQueryParams());
        $paginatedComments = self::createApi($container, CommentsApi::class)
            ->withFilters([
                Comment::FIELD_ID_POST => [
                    FilterParameter::OPERATION_EQUALS => [$index],
                ],
            ])
            ->withIncludes([[Comment::REL_USER]])
            ->withPaging($parser->getPagingOffset(), $parser->getPagingLimit())
            ->index();

        // prepare pagination links
        list(DocumentInterface::KEYWORD_PREV => $prevLink, DocumentInterface::KEYWORD_NEXT => $nextLink) =
            self::getPagingLinks($request->getUri(), $paginatedComments);

        // render HTML body for response
        $body = static::view($container, Views::POST_PAGE, [
            'post'     => $post,
            'comments' => $paginatedComments->getData(),
            'prevLink' => $prevLink,
            'nextLink' => $nextLink,
        ]);

        return new HtmlResponse($body);
    }
}
