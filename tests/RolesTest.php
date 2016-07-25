<?php namespace Tests;

/**
 * @package Tests
 */
class RolesTest extends TestCase
{
    /** API URI */
    const API_URI = '/api/v1/roles';

    /**
     * Test index.
     */
    public function testIndex()
    {
        $response = $this->get(self::API_URI);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));

        $this->assertCount(5, $resources->data);
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
        $this->assertEquals('roles', $resources->data->type);
    }

    /**
     * Test index.
     */
    public function testShowRelationship()
    {
        $response = $this->get(self::API_URI . '/1/relationships/users');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));

        $this->assertCount(2, $resources->data);
    }

    /**
     * Test create and delete.
     */
    public function testCreateAndDelete()
    {
        $this->setPreventCommits();

        $body = <<<EOT
        {
            "data": {
                "type": "roles",
                "id"  : null,
                "attributes": {
                    "name"  : "Role name"
                }
            }
        }
EOT;

        $response = $this->postJson(self::API_URI, $body);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNotEmpty($resource = json_decode((string)$response->getBody()));

        // index of created resource
        $index = $resource->data->id;

        // check it was actually saved in database
        $this->assertEquals(200, $this->get(self::API_URI . "/$index")->getStatusCode());

        // delete
        $this->assertEquals(204, $this->delete(self::API_URI . '/' . $index)->getStatusCode());

        // check resource deleted
        $this->assertEquals(404, $this->get(self::API_URI . "/$index")->getStatusCode());
    }

    /**
     * Test create.
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $index = 1;
        $body  = <<<EOT
        {
            "data" : {
                "type"  : "roles",
                "id"    : "$index",
                "attributes" : {
                    "name"  : "New name"
                }
            }
        }
EOT;

        $response = $this->patchJson(self::API_URI . "/$index", $body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty(json_decode((string)$response->getBody()));

        // check it was actually saved in database
        $connection = $this->getCapturedConnection();
        $name = $connection->executeQuery('SELECT name FROM roles WHERE id_role = ' . $index)->fetchColumn();
        $this->assertEquals('New name', $name);
    }
}
