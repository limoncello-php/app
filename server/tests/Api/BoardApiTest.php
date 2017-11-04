<?php namespace Tests\Api;

use App\Data\Models\Board;
use App\Json\Schemes\BoardScheme;
use Limoncello\Testing\JsonApiCallsTrait;
use Tests\TestCase;

/**
 * @package Tests
 */
class BoardApiTest extends TestCase
{
    use JsonApiCallsTrait;

    const API_URI = '/api/v1/' . BoardScheme::TYPE;

    /**
     * Test Board's API.
     */
    public function testIndex()
    {
        $response = $this->get(self::API_URI);

        $body = (string)$response->getBody();
        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($body);
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(9, $json->data);
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
            'include' => 'posts',        // 'posts.user' or 'posts.user,posts.comments' would also work
        ];
        $response    = $this->get(self::API_URI, $queryParams);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));
        $this->assertCount(3, $resources->data);

        // Board with ID 9 has more than 20 posts. Check that that data in its relationship were paginated
        $resource = $resources->data[0];
        $this->assertEquals(2, $resource->id);
        $this->assertCount(20, $resource->relationships->posts->data);
        $this->assertNotEmpty($resource->relationships->posts->links->next);

        // check response has included posts as well
        $this->assertCount(60, $resources->included);
    }

    /**
     * Test Board's API.
     */
    public function testRead()
    {
        $boardId  = '1';
        $response = $this->get(self::API_URI . "/$boardId");
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($boardId, $json->data->id);
        $this->assertEquals(BoardScheme::TYPE, $json->data->type);
    }

    /**
     * Test index.
     */
    public function testReadRelationship()
    {
        $response = $this->get(self::API_URI . '/1/relationships/' . BoardScheme::REL_POSTS);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));

        $this->assertCount(15, $resources->data);
    }

    /**
     * Test Board's API.
     */
    public function testDelete()
    {
        $this->setPreventCommits();

        $boardId = '2';
        $headers = $this->getAdminOAuthHeader();

        // check board exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$boardId", [], $headers)->getStatusCode());

        // delete
        $this->assertEquals(204, $this->delete(self::API_URI . "/$boardId", [], $headers)->getStatusCode());

        // check board do not exist
        $this->assertEquals(404, $this->get(self::API_URI . "/$boardId", [], $headers)->getStatusCode());
    }

    /**
     * Test Board's API.
     */
    public function testCreate()
    {
        $this->setPreventCommits();

        $title     = "New board";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type"  : "boards",
                "id"    : null,
                "attributes" : {
                    "title"  : "$title",
                    "img-url"  : "/img/tech.jpg"
                }
            }
        }
EOT;
        $headers  = $this->getAdminOAuthHeader();

        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        $this->assertEquals(201, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $boardId = $json->data->id;

        // check board exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$boardId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Board::TABLE_NAME)
            ->where(Board::FIELD_ID . '=' . $query->createPositionalParameter($boardId))
            ->execute();
        $this->assertNotEmpty($statement->fetch());
    }

    /**
     * Test Board's API.
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $index     = 2;
        $title     = "New title";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type"  : "boards",
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

        // check board exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$index", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Board::TABLE_NAME)
            ->where(Board::FIELD_ID . '=' . $query->createPositionalParameter($index))
            ->execute();
        $this->assertNotEmpty($values = $statement->fetch());
        $this->assertEquals($title, $values[Board::FIELD_TITLE]);
        $this->assertNotEmpty($values[Board::FIELD_UPDATED_AT]);
    }
}
