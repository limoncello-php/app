<?php namespace Tests\Api;

use App\Api\RolesApi;
use App\Data\Seeds\RolesSeed;
use Tests\TestCase;

/**
 * @package Tests
 */
class RoleApiTest extends TestCase
{
    /**
     * Shows usage of low level API in tests.
     */
    public function testLowLevelApiRead(): void
    {
        $this->setPreventCommits();

        $this->setModerator();
        $api = $this->createApi(RolesApi::class);

        $roleId = RolesSeed::ROLE_USER;
        $this->assertNotNull($api->read($roleId));
    }

    /**
     * Same test but with auth by a OAuth token.
     */
    public function testLowLevelApiReadWithAuthByToken(): void
    {
        $this->setPreventCommits();

        $oauthToken  = $this->getModeratorOAuthToken();
        $accessToken = $oauthToken->access_token;
        $this->setUserByToken($accessToken);

        $api = $this->createApi(RolesApi::class);

        $roleId = RolesSeed::ROLE_USER;
        $this->assertNotNull($api->read($roleId));
    }
}
