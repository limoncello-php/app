<?php namespace App\Web\Controllers;

use App\Api\BoardsApi;
use App\Api\PostsApi;
use App\Data\Models\Board;
use App\Data\Models\Post;
use App\Web\L10n\Views;
use Limoncello\Flute\Contracts\Http\Controller\ControllerIndexInterface;
use Limoncello\Flute\Contracts\Http\Controller\ControllerReadInterface;
use Limoncello\Flute\Contracts\Http\ControllerInterface;
use Limoncello\Flute\Http\Query\FilterParameter;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class BoardsController extends BaseController implements ControllerIndexInterface, ControllerReadInterface
{
    use PaginationLinksTrait;

    const SUB_URL = '/boards';

    /**
     * @inheritdoc
     */
    public static function index(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        // read resources with pagination and data from relationships
        $parser          = self::createQueryParser($container, $request->getQueryParams());
        $paginatedBoards = self::createApi($container, BoardsApi::class)
            ->withIncludes([[Board::REL_POSTS]])
            ->withPaging($parser->getPagingOffset(), $parser->getPagingLimit())
            ->index();
        $boards          = $paginatedBoards->getData();

        // if no data return 404
        if (empty($boards) === true) {
            return new HtmlResponse(static::view($container, Views::NOT_FOUND_PAGE), 404);
        }

        // now prepare the data for rendering in HTML template

        // limit board's posts to first 3 and prepare pagination links
        foreach ($boards as $board) {
            $board->{Board::REL_POSTS} = array_slice($board->{Board::REL_POSTS}->getData(), 0, 3);
        }
        list(DocumentInterface::KEYWORD_PREV => $prevLink,
            DocumentInterface::KEYWORD_NEXT => $nextLink) = self::getPagingLinks($request->getUri(), $paginatedBoards);

        // render HTML body for response
        $body = static::view($container, Views::BOARDS_PAGE, [
            'boards'   => $boards,
            'prevLink' => $prevLink,
            'nextLink' => $nextLink,
        ]);

        return new HtmlResponse($body);
    }

    /**
     * @inheritdoc
     */
    public static function read(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {

        $index = $routeParams[ControllerInterface::ROUTE_KEY_INDEX];

        $board = self::createApi($container, BoardsApi::class)->read($index);
        // if no data return 404
        if ($board === null) {
            return new HtmlResponse(static::view($container, Views::NOT_FOUND_PAGE), 404);
        }

        // read resource with data from relationships
        $parser         = self::createQueryParser($container, $request->getQueryParams());
        $paginatedPosts = self::createApi($container, PostsApi::class)
            ->withFilters([
                Post::FIELD_ID_BOARD => [
                    FilterParameter::OPERATION_EQUALS => [$index],
                ],
            ])
            ->withIncludes([[Post::REL_USER]])
            ->withPaging($parser->getPagingOffset(), $parser->getPagingLimit())
            ->index();

        // now prepare pagination links
        list(DocumentInterface::KEYWORD_PREV => $prevLink,
            DocumentInterface::KEYWORD_NEXT => $nextLink) = self::getPagingLinks($request->getUri(), $paginatedPosts);

        // render HTML body for response
        $body = static::view($container, Views::BOARD_PAGE, [
            'board'    => $board,
            'posts'    => $paginatedPosts->getData(),
            'prevLink' => $prevLink,
            'nextLink' => $nextLink,
        ]);

        return new HtmlResponse($body);
    }
}
