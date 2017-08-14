<?php namespace Tests;

use App\Data\Models\User;
use App\Data\Seeds\RolesSeed;
use App\Data\Seeds\UsersSeed;
use App\Json\Schemes\UserScheme;
use Limoncello\Testing\JsonApiCallsTrait;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * @package Tests
 */
class UserApiTest extends TestCase
{
    use JsonApiCallsTrait;

    const API_URI = '/api/v1/' . UserScheme::TYPE;

    /**
     * Test User's API.
     */
    public function testIndex()
    {
        $response = $this->get(self::API_URI, [], ['Authorization' => 'Bearer ' . $this->getAdminOAuthToken()]);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(UsersSeed::NUMBER_OF_RECORDS, $json->data);
    }

    /**
     * Test User's API.
     */
    public function testIndexInvalidToken()
    {
        $response = $this->get(self::API_URI, [], ['Authorization' => 'Bearer ' . $this->getAdminOAuthToken() . 'XXX']);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test User's API.
     */
    public function testRead()
    {
        $userId   = '1';
        $headers  = ['Authorization' => 'Bearer ' . $this->getAdminOAuthToken()];
        $response = $this->get(self::API_URI . "/$userId", [], $headers);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($userId, $json->data->id);
        $this->assertEquals(UserScheme::TYPE, $json->data->type);
    }

    /**
     * Test User's API.
     */
    public function testDelete()
    {
        $this->setPreventCommits();

        $userId  = '2';
        $headers = ['Authorization' => 'Bearer ' . $this->getAdminOAuthToken()];

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // delete
        $this->assertEquals(204, $this->delete(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // check user do not exist
        $this->assertEquals(404, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());
    }

    /**
     * Test User's API.
     */
    public function testCreate()
    {
        $this->setPreventCommits();

        $password  = 'secret';
        $email     = "john@dow.foo";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "users",
                "attributes" : {
                    "first-name" : "John",
                    "last-name"  : "Dow",
                    "email"      : "$email",
                    "password"   : "$password"
                },
                "relationships": {
                    "role": {
                        "data": { "type": "roles", "id": "user" }
                    }
                }
            }
        }
EOT;
        $headers = ['Authorization' => 'Bearer ' . $this->getAdminOAuthToken()];

        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        $this->assertEquals(201, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $userId = $json->data->id;

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(User::TABLE_NAME)
            ->where(User::FIELD_ID . '=' . $query->createPositionalParameter($userId))
            ->execute();
        $this->assertNotEmpty($statement->fetch());
    }

    /**
     * Test User's API.
     */
    public function testCreateInvalidData()
    {
        $this->setPreventCommits();

        $password  = 'secret';
        $email     = "it_does_not_look_like_an_email";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "users",
                "attributes" : {
                    "first-name" : "John",
                    "last-name"  : "Dow",
                    "email"      : "$email",
                    "password"   : "$password"
                },
                "relationships": {
                    "role": {
                        "data": { "type": "roles", "id": "user" }
                    }
                }
            }
        }
EOT;
        $headers = ['Authorization' => 'Bearer ' . $this->getAdminOAuthToken()];

        $exception = null;
        try {
            $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        } catch (JsonApiException $exception) {
        }

        $this->assertNotNull($exception);
        $this->assertCount(1, $exception->getErrors());
        $error = $exception->getErrors()->getArrayCopy()[0];
        $this->assertEquals('The value should be a valid email address.', $error->getDetail());
    }

    /**
     * Test User's API.
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $userId = 2;
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "users",
                "id"   : "$userId",
                "attributes" : {
                    "first-name" : "John",
                    "last-name"  : "Dow",
                    "password"   : "new-secret"
                },
                "relationships": {
                    "role": {
                        "data": { "type": "roles", "id": "user" }
                    }
                }
            }
        }
EOT;
        $headers = ['Authorization' => 'Bearer ' . $this->getAdminOAuthToken()];

        $response = $this->patchJsonApi(self::API_URI . "/$userId", $jsonInput, $headers);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($userId, $json->data->id);

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(User::TABLE_NAME)
            ->where(User::FIELD_ID . '=' . $query->createPositionalParameter($userId))
            ->execute();
        $this->assertNotEmpty($values = $statement->fetch());
        $this->assertEquals('John', $values[User::FIELD_FIRST_NAME]);
        $this->assertEquals('Dow', $values[User::FIELD_LAST_NAME]);
        $this->assertNotEmpty($values[User::FIELD_UPDATED_AT]);
        $this->assertEquals(RolesSeed::ROLE_USER, $values[User::FIELD_ID_ROLE]);
    }

    /**
     * Test User's API.
     *
     * @expectedException \Neomerx\JsonApi\Exceptions\JsonApiException
     */
    public function testUnauthorizedDenied()
    {
        // no token header
        $this->get(self::API_URI);
    }
}
