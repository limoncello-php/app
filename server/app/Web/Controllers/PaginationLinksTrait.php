<?php namespace App\Web\Controllers;

use Limoncello\Flute\Contracts\Adapters\PaginationStrategyInterface;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Neomerx\JsonApi\Contracts\Http\Query\BaseQueryParserInterface;
use Psr\Http\Message\UriInterface;

/**
 * @package App
 */
trait PaginationLinksTrait
{
    /**
     * @param UriInterface           $originalUri
     * @param PaginatedDataInterface $data
     *
     * @return UriInterface[]
     */
    private static function getPagingLinks(UriInterface $originalUri, PaginatedDataInterface $data): array
    {
        $links = [
            DocumentInterface::KEYWORD_PREV => null,
            DocumentInterface::KEYWORD_NEXT => null,
        ];

        if ($data->isCollection() === true && (0 < $data->getOffset() || $data->hasMoreItems() === true)) {
            parse_str($originalUri->getQuery(), $queryParams);

            $pageSize    = $data->getLimit();
            $linkClosure = function (int $offset) use ($originalUri, $pageSize, $queryParams): UriInterface {
                $paramsWithPaging = array_merge($queryParams, [
                    BaseQueryParserInterface::PARAM_PAGE => [
                        PaginationStrategyInterface::PARAM_PAGING_OFFSET => $offset,
                        PaginationStrategyInterface::PARAM_PAGING_LIMIT  => $pageSize,
                    ],
                ]);

                $paginatedUri = $originalUri->withQuery(http_build_query($paramsWithPaging));

                return $paginatedUri;
            };

            if ($data->getOffset() > 0) {
                $prevOffset                             = max(0, $data->getOffset() - $data->getLimit());
                $links[DocumentInterface::KEYWORD_PREV] = $linkClosure($prevOffset);
            }
            if ($data->hasMoreItems() === true) {
                $nextOffset                             = $data->getOffset() + $data->getLimit();
                $links[DocumentInterface::KEYWORD_NEXT] = $linkClosure($nextOffset);
            }
        }

        return $links;
    }
}
