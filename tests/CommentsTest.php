<?php namespace Tests;

use App\Schemes\CommentSchema;
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
    public function testIndex()
    {
        $response = $this->get(self::API_URI);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));

        // by default results are paginated by 10 resources
        $this->assertCount(10, $resources->data);
    }

    /**
     * Test index.
     */
    public function testShow()
    {
        $response = $this->get(self::API_URI . '/2');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));

        $this->assertEquals(2, $resources->data->id);
        $this->assertEquals('comments', $resources->data->type);
    }

    /**
     * Test create and delete.
     */
    public function testCreateAndDelete()
    {
        $this->setPreventCommits();
        $authHeaders = $this->createAdminAuthHeaders();

        // Note you can save `belongsTo` relationships on creation (`belongsToMany` is also supported).
        //
        // `hasMany` could not be saved by it nature as it requires
        // saving additional resources (we can return ID for only 1 created resource per HTTP request).
        $body = <<<EOT
        {
            "data": {
                "type": "comments",
                "id"  : null,
                "attributes": {
                    "text"  : "Comment text"
                },
                "relationships": {
                    "post": {
                        "data": { "type": "posts", "id": "1" }
                    }
                }
            }
        }
EOT;

        $response = $this->postJson(self::API_URI, $body, $authHeaders);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNotEmpty($resource = json_decode((string)$response->getBody()));

        // index of created resource
        $index = $resource->data->id;

        // check it was actually saved in database
        $this->assertEquals(200, $this->get(self::API_URI . "/$index")->getStatusCode());

        // delete
        $this->assertEquals(204, $this->delete(self::API_URI . '/' . $index, [], $authHeaders)->getStatusCode());

        // check resource deleted
        $this->assertEquals(404, $this->get(self::API_URI . "/$index")->getStatusCode());
    }

    /**
     * Test create.
     */
    public function testUpdate()
    {
        $this->setPreventCommits();
        $authHeaders = $this->createAdminAuthHeaders();

        $index = 1;
        $body  = <<<EOT
        {
            "data" : {
                "type"  : "comments",
                "id"    : "$index",
                "attributes" : {
                    "text"  : "New text"
                }
            }
        }
EOT;

        $response = $this->patchJson(self::API_URI . "/$index", $body, $authHeaders);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty(json_decode((string)$response->getBody()));

        // check it was actually saved in database
        $connection = $this->getCapturedConnection();
        $title = $connection->executeQuery('SELECT `text` FROM comments WHERE id_comment = ' . $index)->fetchColumn();
        $this->assertEquals('New text', $title);
    }

    /**
     * Test index.
     */
    public function testHasTopLevelMeta()
    {
        $response = $this->get(self::API_URI);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));
        $this->assertNotEmpty($resources->meta);
    }

    /**
     * Test index.
     */
    public function testLimitsTopLevelResourcesWithPagination()
    {
        $response = $this->get(self::API_URI);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resource = json_decode((string)$response->getBody()));
        // check value from config was used
        $this->assertCount(10, $resource->data);
        $this->assertNotEmpty($resource->links);
        $this->assertNotEmpty($resource->links->next);
        $this->assertObjectNotHasAttribute('prev', $resource->links);
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

        // check requested crazy number of resources was limited by backend
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
        $this->assertCount(2, $resource->data);
    }
}
