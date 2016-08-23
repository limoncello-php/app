<?php namespace Tests;

use App\Schemes\PostSchema;

/**
 * @package Tests
 */
class PostsTest extends TestCase
{
    /** API URI */
    const API_URI = '/api/v1/posts';

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
        $this->assertEquals('posts', $resources->data->type);
    }

    /**
     * Test index.
     */
    public function testShowRelationship()
    {
        $response = $this->get(self::API_URI . '/2/relationships/' . PostSchema::REL_COMMENTS);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));

        $this->assertNotEmpty($resources->data);
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
                "type": "posts",
                "id"  : null,
                "attributes": {
                    "title" : "Post title",
                    "text"  : "Post text"
                },
                "relationships": {
                    "board": {
                        "data": { "type": "boards", "id": "1" }
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

        $index = 2;
        $body  = <<<EOT
        {
            "data" : {
                "type"  : "posts",
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
        $text = $connection->executeQuery('SELECT `text` FROM posts WHERE id_post = ' . $index)->fetchColumn();
        $this->assertEquals('New text', $text);
    }
}
