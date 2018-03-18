<?php namespace Tests\Api;

use App\Data\Models\Comment;
use App\Json\Schemes\CommentSchema;
use Limoncello\Testing\JsonApiCallsTrait;
use Tests\TestCase;

/**
 * @package Tests
 */
class CommentApiTest extends TestCase
{
    use JsonApiCallsTrait;

    const API_URI = '/api/v1/' . CommentSchema::TYPE;

    /**
     * Test Comment's API.
     */
    public function testIndex()
    {
        $response = $this->get(self::API_URI);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(10, $json->data);
    }

    /**
     * Test index with parameters.
     */
    public function testIndexWithFilterAndInclude()
    {
        $queryParams = [
            'filter' => [
                'id' => [
                    'greater-than' => '1', // 'long' form for condition operations
                    'lte'          => '4', // 'short' form supported as well
                ],
            ],
            'sort'   => '+id,-text',   // example of how multiple sorting conditions could be applied
        ];
        $response    = $this->get(self::API_URI, $queryParams);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));
        $this->assertCount(3, $resources->data);

        $resource = $resources->data[0];
        $this->assertEquals(2, $resource->id);
    }

    /**
     * Test index with parameters.
     */
    public function testIndexWithAuthorFilter()
    {
        $queryParams = [
            'filter' => [
                'post' => [
                    'eq' => '1',
                ],
                'user' => [
                    'eq' => '2',
                ],
            ],
            'sort'   => '-created-at',   // example of how multiple sorting conditions could be applied
        ];
        $response    = $this->get(self::API_URI, $queryParams);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));
        $this->assertCount(2, $resources->data);
    }

    /**
     * Test index with parameters.
     */
    public function testIndexWithTooManyItems()
    {
        $queryParams = [
            'page' => [
                'limit' => '10000',
            ],
        ];
        $response    = $this->get(self::API_URI, $queryParams);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertNotNull($errors = json_decode((string)$response->getBody()));
        $this->assertCount(1, $errors->errors);
        $this->assertContains('The value should be between', $errors->errors[0]->detail);
    }

    /**
     * Test Comment's API.
     */
    public function testRead()
    {
        $commentId = '1';
        $response  = $this->get(self::API_URI . "/$commentId");
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($commentId, $json->data->id);
        $this->assertEquals(CommentSchema::TYPE, $json->data->type);
    }

    /**
     * Test Comment's API.
     */
    public function testDelete()
    {
        $this->setPreventCommits();

        $commentId = '2';
        $headers   = $this->getAdminOAuthHeader();

        // check comment exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$commentId", [], $headers)->getStatusCode());

        // delete
        $this->assertEquals(204, $this->delete(self::API_URI . "/$commentId", [], $headers)->getStatusCode());

        // check comment do not exist
        $this->assertEquals(404, $this->get(self::API_URI . "/$commentId", [], $headers)->getStatusCode());
    }

    /**
     * Test Comment's API.
     */
    public function testCreate()
    {
        $this->setPreventCommits();

        $text      = "Comment text";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type"  : "comments",
                "id"    : null,
                "attributes" : {
                    "text"  : "$text"
                },
                "relationships": {
                    "post": {
                        "data": { "type": "posts", "id": "1" }
                    }
                }
            }
        }
EOT;
        $headers   = $this->getAdminOAuthHeader();

        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        $this->assertEquals(201, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $commentId = $json->data->id;

        // check comment exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$commentId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Comment::TABLE_NAME)
            ->where(Comment::FIELD_ID . '=' . $query->createPositionalParameter($commentId))
            ->execute();
        $this->assertNotEmpty($statement->fetch());
    }

    /**
     * Test Comment's API.
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $index     = 2;
        $text      = "New text";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type"  : "comments",
                "id"    : "$index",
                "attributes" : {
                    "text"   : "$text"
                }
            }
        }
EOT;
        $headers   = $this->getAdminOAuthHeader();

        $response = $this->patchJsonApi(self::API_URI . "/$index", $jsonInput, $headers);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($index, $json->data->id);

        // check comment exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$index", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Comment::TABLE_NAME)
            ->where(Comment::FIELD_ID . '=' . $query->createPositionalParameter($index))
            ->execute();
        $this->assertNotEmpty($values = $statement->fetch());
        $this->assertEquals($text, $values[Comment::FIELD_TEXT]);
        $this->assertNotEmpty($values[Comment::FIELD_UPDATED_AT]);
    }
}
