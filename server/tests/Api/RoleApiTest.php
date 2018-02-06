<?php namespace Tests\Api;

use App\Data\Models\Role;
use App\Data\Seeds\RolesSeed;
use App\Json\Schemes\RoleSchema;
use Limoncello\Testing\JsonApiCallsTrait;
use Tests\TestCase;

/**
 * @package Tests
 */
class RoleApiTest extends TestCase
{
    use JsonApiCallsTrait;

    const API_URI = '/api/v1/' . RoleSchema::TYPE;

    /**
     * Test Role's API.
     */
    public function testIndex()
    {
        $response = $this->get(self::API_URI, [], $this->getModeratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(3, $json->data);
    }

    /**
     * Test Role's API.
     */
    public function testRead()
    {
        $roleId   = RolesSeed::ROLE_USER;
        $response = $this->get(self::API_URI . "/$roleId", [], $this->getModeratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($roleId, $json->data->id);
        $this->assertEquals(RoleSchema::TYPE, $json->data->type);
    }

    /**
     * Test Role's API.
     */
    public function testCreate()
    {
        $this->setPreventCommits();

        $description = "New role";
        $jsonInput   = <<<EOT
        {
            "data" : {
                "type"  : "roles",
                "id"    : "new_role",
                "attributes" : {
                    "description"  : "$description"
                }
            }
        }
EOT;
        $headers  = $this->getAdminOAuthHeader();

        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        $this->assertEquals(201, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $roleId = $json->data->id;

        // check role exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$roleId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Role::TABLE_NAME)
            ->where(Role::FIELD_ID . '=' . $query->createPositionalParameter($roleId))
            ->execute();
        $this->assertNotEmpty($statement->fetch());
    }

    /**
     * Test Role's API.
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $index       = RolesSeed::ROLE_USER;
        $description = "New description";
        $jsonInput   = <<<EOT
        {
            "data" : {
                "type"  : "roles",
                "id"    : "$index",
                "attributes" : {
                    "description" : "$description"
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

        // check role exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$index", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Role::TABLE_NAME)
            ->where(Role::FIELD_ID . '=' . $query->createPositionalParameter($index))
            ->execute();
        $this->assertNotEmpty($values = $statement->fetch());
        $this->assertEquals($description, $values[Role::FIELD_DESCRIPTION]);
        $this->assertNotEmpty($values[Role::FIELD_UPDATED_AT]);
    }
}
