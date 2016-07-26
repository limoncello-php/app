<?php namespace Tests;

/**
 * @package Tests
 */
class BoardsTest extends TestCase
{
    /** API URI */
    const API_URI = '/api/v1/boards';

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
     * Test index with parameters.
     */
    public function testIndexWithInclude()
    {
        $queryParams = [
            'filter'  => [
                'id' => [
                    'greater-than' => 5, // 'long' form for condition operations
                    'lte'          => 9, // 'short' form supported as well
                ],
            ],
            'sort'    => '-id,+title',   // example of how multiple sorting conditions could be applied
            'include' => 'posts',        // 'posts.user' or 'posts.user,posts.comments' would also work
        ];
        $response = $this->get(self::API_URI, $queryParams);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));
        $this->assertCount(4, $resources->data);

        // Board with ID 9 has more than 10 posts. Check that that data in its relationship were paginated
        $resource = $resources->data[0];
        $this->assertEquals(9, $resource->id);
        $this->assertCount(10, $resource->relationships->posts->data);
        $this->assertNotEmpty($resource->relationships->posts->links->next);

        // check response has included posts as well
        $this->assertCount(35, $resources->included);
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
        $this->assertEquals('boards', $resources->data->type);
    }

    /**
     * Test index.
     */
    public function testShowRelationship()
    {
        $response = $this->get(self::API_URI . '/2/relationships/posts');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));

        $this->assertCount(7, $resources->data);
    }

    /**
     * Test create and delete.
     */
    public function testCreateAndDelete()
    {
        $this->setPreventCommits();
        $authHeaders = $this->createAdminAuthHeaders();

        $body = <<<EOT
        {
            "data" : {
                "type"  : "boards",
                "id"    : null,
                "attributes" : {
                    "title"  : "New board"
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
        // option 1 - direct database query
        $connection = $this->getCapturedConnection();
        $count = $connection->executeQuery('SELECT count(*) FROM boards WHERE id_board = ' . $index)->fetchColumn();
        $this->assertEquals(1, $count);
        // option 2 - using APi
        $this->assertEquals(200, $this->get(self::API_URI . "/$index")->getStatusCode());

        // delete
        $this->assertEquals(204, $this->delete(self::API_URI . '/' . $index, [], $authHeaders)->getStatusCode());

        // check resource deleted
        // option 1 - direct database query
        $count = $connection->executeQuery('SELECT count(*) FROM boards WHERE id_board = ' . $index)->fetchColumn();
        $this->assertEquals(0, $count);
        // option 2 - using APi
        $this->assertEquals(404, $this->get(self::API_URI . "/$index")->getStatusCode());

        // check multi-delete do not cause any problems
        $this->assertEquals(204, $this->delete(self::API_URI . '/' . $index, [], $authHeaders)->getStatusCode());
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
                "type"  : "boards",
                "id"    : "$index",
                "attributes" : {
                    "title"  : "New title"
                }
            }
        }
EOT;

        $response = $this->patchJson(self::API_URI . "/$index", $body, $authHeaders);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty(json_decode((string)$response->getBody()));

        // check it was actually saved in database
        $connection = $this->getCapturedConnection();
        $title = $connection->executeQuery('SELECT title FROM boards WHERE id_board = ' . $index)->fetchColumn();
        $this->assertEquals('New title', $title);
    }
}
