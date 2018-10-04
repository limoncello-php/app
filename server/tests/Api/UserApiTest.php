<?php namespace Tests\Api;

use App\Api\UsersApi;
use Limoncello\Testing\JsonApiCallsTrait;
use Tests\TestCase;

/**
 * @package Tests
 */
class UserApiTest extends TestCase
{
    use JsonApiCallsTrait;

    /**
     * Test User's API.
     */
    public function testIndex()
    {
        $this->setPreventCommits();

        /** @var UsersApi $api */
        $api = $this->createApi(UsersApi::class);

        /** Default seed data. Manually checked. */
        $this->assertEquals(5, $api->noAuthReadUserIdByEmail('denesik.stewart@gmail.com'));
    }
}
