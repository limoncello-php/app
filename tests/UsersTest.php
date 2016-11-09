<?php namespace Tests;

use App\Http\Controllers\UsersController;
use App\Schemes\UserSchema;

/**
 * @package Tests
 */
class UsersTest extends TestCase
{
    /** API URI */
    const API_URI = '/api/v1/users';

    /**
     * Test index.
     */
    public function testIndex()
    {
        $response = $this->get(self::API_URI);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));

        // by default results are paginated by 10 resources
        $this->assertCount(2, $resources->data);
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
        $this->assertEquals('users', $resources->data->type);
    }

    /**
     * Test index.
     */
    public function testShowRelationship()
    {
        $response = $this->get(self::API_URI . '/2/relationships/' . UserSchema::REL_POSTS);

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
                "type": "users",
                "id"  : null,
                "attributes": {
                    "title"      : "User title",
                    "first-name" : "John",
                    "last-name"  : "Dow",
                    "language"   : "en",
                    "email"      : "john@dow.foo",
                    "password"   : "secret"
                },
                "relationships": {
                    "role": {
                        "data": { "type": "roles", "id": "1" }
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
                "type"  : "users",
                "id"    : "$index",
                "attributes" : {
                    "first-name" : "New name"
                }
            }
        }
EOT;

        $response = $this->patchJson(self::API_URI . "/$index", $body, $authHeaders);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty(json_decode((string)$response->getBody()));

        // check it was actually saved in database
        $connection = $this->getCapturedConnection();
        $name = $connection->executeQuery('SELECT first_name FROM users WHERE id_user = ' . $index)->fetchColumn();
        $this->assertEquals('New name', $name);
    }

    /**
     * Test user authentication.
     */
    public function testAuthentication()
    {
        $this->setPreventCommits();

        $token = $this->setUserToken('admin@admins.tld', 'password');

        // test request with token
        $response = $this->get(self::API_URI, [], $this->getAuthorizationHeaders($token));
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test user authentication.
     */
    public function testInvalidAuthentication()
    {
        $formData = [
            UsersController::FORM_EMAIL    => 'admin@admins.tld',
            UsersController::FORM_PASSWORD => 'password-XXX',
        ];
        $response = $this->post('/authenticate', $formData);
        $this->assertEquals(401, $response->getStatusCode());
    }
}
