<?php namespace Tests\Api;

use App\Data\Models\Post;
use App\Json\Schemes\PostScheme;
use Limoncello\Testing\JsonApiCallsTrait;
use Tests\TestCase;

/**
 * @package Tests
 */
class PostApiTest extends TestCase
{
    use JsonApiCallsTrait;

    const API_URI = '/api/v1/' . PostScheme::TYPE;

    /**
     * Test Post's API.
     */
    public function testIndex()
    {
        $response = $this->get(self::API_URI);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(20, $json->data);
    }

    /**
     * Test index with parameters.
     */
    public function testIndexWithInclude()
    {
        $queryParams = [
            'filter'  => [
                'id' => [
                    'greater-than' => '1', // 'long' form for condition operations
                    'lte'          => '4', // 'short' form supported as well
                ],
            ],
            'sort'    => '+id,-title',   // example of how multiple sorting conditions could be applied

            // example of how to add includes
            // also the controller will limit the deepness of include paths and ignore anything deeper than first level
            'include' => 'comments,comments.user,comments.post',
        ];
        $response = $this->get(self::API_URI, $queryParams);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));
        $this->assertCount(3, $resources->data);

        // Post with ID 9 has more than 20 posts. Check that that data in its relationship were paginated
        $resource = $resources->data[0];
        $this->assertEquals(2, $resource->id);
        $this->assertCount(4, $resource->relationships->comments->data);

        // check response has included posts as well and ignored deeper paths
        $this->assertCount(9, $resources->included);
    }

    /**
     * Test Post's API.
     */
    public function testRead()
    {
        $postId   = '1';
        $response = $this->get(self::API_URI . "/$postId");
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($postId, $json->data->id);
        $this->assertEquals(PostScheme::TYPE, $json->data->type);
    }

    /**
     * Test index.
     */
    public function testReadRelationship()
    {
        $response = $this->get(self::API_URI . '/10/relationships/' . PostScheme::REL_COMMENTS);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));

        $this->assertCount(3, $resources->data);
    }

    /**
     * Test Post's API.
     */
    public function testDelete()
    {
        $this->setPreventCommits();

        $postId  = '2';
        $headers = $this->getAdminOAuthHeader();

        // check post exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$postId", [], $headers)->getStatusCode());

        // delete
        $this->assertEquals(204, $this->delete(self::API_URI . "/$postId", [], $headers)->getStatusCode());

        // check post do not exist
        $this->assertEquals(404, $this->get(self::API_URI . "/$postId", [], $headers)->getStatusCode());
    }

    /**
     * Test Post's API.
     */
    public function testCreate()
    {
        $this->setPreventCommits();

        $title     = "New post";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type"  : "posts",
                "id"    : null,
                "attributes" : {
                    "title"  : "$title",
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
        $headers  = $this->getAdminOAuthHeader();

        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        $this->assertEquals(201, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $postId = $json->data->id;

        // check post exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$postId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Post::TABLE_NAME)
            ->where(Post::FIELD_ID . '=' . $query->createPositionalParameter($postId))
            ->execute();
        $this->assertNotEmpty($statement->fetch());
    }

    /**
     * Test Post's API.
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $index     = 2;
        $title     = "New title";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type"  : "posts",
                "id"    : "$index",
                "attributes" : {
                    "title"  : "$title"
                }
            }
        }
EOT;
        $headers  = $this->getAdminOAuthHeader();

        $response = $this->patchJsonApi(self::API_URI . "/$index", $jsonInput, $headers);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($index, $json->data->id);

        // check post exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$index", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Post::TABLE_NAME)
            ->where(Post::FIELD_ID . '=' . $query->createPositionalParameter($index))
            ->execute();
        $this->assertNotEmpty($values = $statement->fetch());
        $this->assertEquals($title, $values[Post::FIELD_TITLE]);
        $this->assertNotEmpty($values[Post::FIELD_UPDATED_AT]);
    }
}
