<?php namespace Tests\Api;

use App\Api\UsersApi;
use App\Data\Models\User;
use Doctrine\DBAL\DBALException;
use Limoncello\Contracts\Exceptions\AuthorizationExceptionInterface;
use Tests\TestCase;
use function assert;

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
        $api = $this->createUsersApi();

        // Call and check any method from low level API.

        /** Default seed data. Manually checked. */
        $this->assertEquals(5, $api->noAuthReadUserIdByEmail('denesik.stewart@gmail.com'));
    }

    /**
     * Test for password reset.
     *
     * @throws DBALException
     * @throws AuthorizationExceptionInterface
     */
    public function testResetPassword()
    {
        $this->setPreventCommits();

        // create APIs

        $noAuthApi = $this->createUsersApi();

        $this->setAdmin();
        $api = $this->createUsersApi();

        // Call reset method.
        $userId = 1;
        $before = $api->read($userId);
        $this->assertTrue($noAuthApi->noAuthResetPassword($userId, 'new password'));
        $after = $api->read($userId);
        $this->assertNotEquals($before->{User::FIELD_PASSWORD_HASH}, $after->{User::FIELD_PASSWORD_HASH});
    }

    /**
     * @return UsersApi
     */
    private function createUsersApi(): UsersApi
    {
        $api = $this->createApi(UsersApi::class);
        assert($api instanceof UsersApi);

        return $api;
    }

}
