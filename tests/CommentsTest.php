<?php namespace Tests;

use Limoncello\JsonApi\Adapters\PaginationStrategy;

/**
 * @package Tests
 */
class CommentsTest extends TestCase
{
    /** API URI */
    const API_URI = '/api/v1/comments';

    /**
     * Test index.
     */
    public function testLimitsTopLevelResourcesWithPagination()
    {
        $response = $this->get(self::API_URI);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resource = json_decode((string)$response->getBody()));
        $this->assertCount(PaginationStrategy::DEFAULT_LIMIT_SIZE, $resource->data);
        $this->assertNotEmpty($resource->links);
        $this->assertNotEmpty($resource->links->next);
        $this->assertEmpty($resource->links->prev);
    }

    /**
     * Test index.
     */
    public function testInvalidPaginationParameters1()
    {
        parse_str('page[size]=100000&page[skip]=5', $parameters);
        $response = $this->get(self::API_URI, $parameters);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resource = json_decode((string)$response->getBody()));
        $this->assertCount(PaginationStrategy::DEFAULT_LIMIT_SIZE, $resource->data);
        $this->assertNotEmpty($resource->links->next);
        $this->assertNotEmpty($resource->links->prev);
    }

    /**
     * Test index.
     */
    public function testInvalidPaginationParameters2()
    {
        parse_str('page[size]=-5&page[skip]=5', $parameters);
        $response = $this->get(self::API_URI, $parameters);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resource = json_decode((string)$response->getBody()));
        $this->assertCount(PaginationStrategy::DEFAULT_LIMIT_SIZE, $resource->data);
        $this->assertNotEmpty($resource->links->next);
        $this->assertNotEmpty($resource->links->prev);
    }

    /**
     * Test filter by multiple conditions for 1 field.
     */
    public function testMultiLikeSelect()
    {
        parse_str('filter[text][like][0]=%porro%&filter[text][like][1]=%velit%', $parameters);
        $response = $this->get(self::API_URI, $parameters);


        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resource = json_decode((string)$response->getBody()));

        // manually checked number comments that have 'porro' and `velit' in `text` field
        $this->assertCount(6, $resource->data);
    }
}
