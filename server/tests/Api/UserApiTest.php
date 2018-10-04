<?php namespace Tests\Api;

use App\Api\UsersApi;
use Tests\TestCase;

/**
 * @package Tests
 */
class UserApiTest extends TestCase
{
    /**
     * Sample how to test low level API.
     */
    public function testLowLevelApi()
    {
        $this->setPreventCommits();

        // create API

        /** @var UsersApi $api */
        $api = $this->createApi(UsersApi::class);

        // Call and check any method from low level API.

        /** Default seed data. Manually checked. */
        $this->assertEquals(5, $api->noAuthReadUserIdByEmail('denesik.stewart@gmail.com'));
    }
}
