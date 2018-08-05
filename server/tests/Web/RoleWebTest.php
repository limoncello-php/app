<?php namespace Tests\Web;

use App\Data\Models\Role;
use App\Data\Seeds\RolesSeed;
use App\Json\Schemas\RoleSchema;
use Limoncello\Application\Packages\Csrf\CsrfSettings;
use Tests\TestCase;

/**
 * @package Tests
 */
class RoleWebTest extends TestCase
{
    const RESOURCES_URL = '/roles';

    /**
     * Test index page.
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $response = $this->get(static::RESOURCES_URL, [], [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test index page.
     *
     * @return void
     */
    public function testIndexWithFilters(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $queryParams = [
            'filter' => [
                RoleSchema::ATTR_DESCRIPTION => [
                    'like' => '%a%',
                ],
                RoleSchema::ATTR_CREATED_AT  => [
                    'less-than' => '2100-02-03T04:05:06+0000',
                ],
            ],
        ];

        // execution time measurement example
        $response = $this->get(self::RESOURCES_URL, $queryParams, [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $this->assertContains('Administrator', $body);
        $this->assertContains('data-role-id="moderator"', $body);
        $this->assertNotContains('data-role-id="user"', $body);
    }

    /**
     * Test index page.
     *
     * @return void
     */
    public function testIndexWithPagination(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $queryParams = [
            'page' => [
                'offset' => '1',
                'limit'  => '1',
            ],
        ];

        // execution time measurement example
        $response = $this->get(self::RESOURCES_URL, $queryParams, [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $this->assertContains('Prev', $body);
        $this->assertContains('Next', $body);
    }

    /**
     * Test index page.
     *
     * @return void
     */
    public function testIndexForbiddenForGuests(): void
    {
        $response = $this->get(static::RESOURCES_URL);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test index page.
     *
     * @return void
     */
    public function testIndexAllowedForModerators(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getModeratorOAuthCookie();

        $response = $this->get(static::RESOURCES_URL, [], [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test open edit role form.
     *
     * @return void
     */
    public function testOpenEditForm(): void
    {
        $roleId = RolesSeed::ROLE_MODERATOR;

        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $response = $this->get(self::RESOURCES_URL . '/' . $roleId, [], [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Update', (string)$response->getBody());
        $this->assertContains('Delete', (string)$response->getBody());
    }

    /**
     * Test open edit role form.
     *
     * @return void
     */
    public function testOpenEditFormModeratorCanOnlyRead(): void
    {
        $roleId = RolesSeed::ROLE_MODERATOR;

        $this->setPreventCommits();

        $authCookie = $this->getModeratorOAuthCookie();

        $response = $this->get(self::RESOURCES_URL . '/' . $roleId, [], [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotContains('Update', (string)$response->getBody());
        $this->assertNotContains('Delete', (string)$response->getBody());
    }

    /**
     * Test open edit role form.
     *
     * @return void
     */
    public function testOpenEditFormForbiddenForUsers(): void
    {
        $roleId = 3;

        $this->setPreventCommits();

        $authCookie = $this->getPlainUserOAuthCookie();

        $response = $this->get(self::RESOURCES_URL . '/' . $roleId, [], [], $authCookie);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test open create role form.
     *
     * @return void
     */
    public function testOpenCreateForm(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $response = $this->get(self::RESOURCES_URL . '/create', [], [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Create', (string)$response->getBody());
        $this->assertNotContains('Delete', (string)$response->getBody());
        $this->assertNotContains('Update', (string)$response->getBody());
    }

    /**
     * Test post 'create role' form data.
     *
     * @return void
     */
    public function testPostCreateFormWithValidData(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $data = [
            RoleSchema::RESOURCE_ID      => 'test-id',
            RoleSchema::ATTR_DESCRIPTION => 'Test Role',

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'anything',
        ];

        // make CSRF protection check successful
        $this->passThroughCsrfOnNextAppCall();

        $response = $this->post(self::RESOURCES_URL . '/create', $data, [], $authCookie);

        // check we've got redirected on success
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test post 'create role' form data.
     *
     * @return void
     */
    public function testPostCreateFormForbiddenForModerators(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getModeratorOAuthCookie();

        $data = [
            RoleSchema::RESOURCE_ID      => 'test-id',
            RoleSchema::ATTR_DESCRIPTION => 'Test Role',
        ];

        $response = $this->post(self::RESOURCES_URL . '/create', $data, [], $authCookie);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test post 'create role' form data.
     *
     * @return void
     */
    public function testPostCreateFormWithInvalidData(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $data = [
            RoleSchema::RESOURCE_ID      => '', // <-- no role ID
            RoleSchema::ATTR_DESCRIPTION => 'Test Role',

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'anything',
        ];

        // make CSRF protection check successful
        $this->passThroughCsrfOnNextAppCall();

        $response = $this->post(self::RESOURCES_URL . '/create', $data, [], $authCookie);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertContains('The value should be between 1 and 255 characters.', (string)$response->getBody());
        $this->assertContains('Create', (string)$response->getBody());
        $this->assertNotContains('Delete', (string)$response->getBody());
        $this->assertNotContains('Update', (string)$response->getBody());
    }

    /**
     * Test post 'update role' form data.
     *
     * @return void
     */
    public function testPostUpdateFormWithValidData(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $data = [
            RoleSchema::ATTR_DESCRIPTION => 'Test Role',

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'anything',
        ];

        // make CSRF protection check successful
        $this->passThroughCsrfOnNextAppCall();

        $roleId   = RolesSeed::ROLE_USER;
        $response = $this->post(self::RESOURCES_URL . "/$roleId", $data, [], $authCookie);

        // check we've got redirected on success
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test post 'update role' form data.
     *
     * @return void
     */
    public function testPostUpdateFormWithInvalidData(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $data = [
            RoleSchema::ATTR_DESCRIPTION => '', // <-- empty

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'anything',
        ];

        // make CSRF protection check successful
        $this->passThroughCsrfOnNextAppCall();

        $roleId   = RolesSeed::ROLE_USER;
        $response = $this->post(self::RESOURCES_URL . "/$roleId", $data, [], $authCookie);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertContains('The value should be between 1 and 255 characters', (string)$response->getBody());
    }

    /**
     * Test delete role.
     *
     * @return void
     */
    public function testDeleteRole(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $data = [
            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'anything',
        ];

        // make CSRF protection check successful
        $this->passThroughCsrfOnNextAppCall();

        $roleId   = RolesSeed::ROLE_USER;
        $response = $this->post(self::RESOURCES_URL . "/$roleId/delete", $data, [], $authCookie);

        // check we've got redirected on success
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test index page.
     *
     * @return void
     */
    public function testIndexUsersRelationship(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getAdminOAuthCookie();

        $role         = RolesSeed::ROLE_USER;
        $relationship = Role::REL_USERS;
        $response     = $this->get(static::RESOURCES_URL . "/$role/$relationship", [], [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());

        // one of the default users
        $this->assertContains('bettie14@gmail.com', (string)$response->getBody());

        // one of the default moderators
        $this->assertNotContains('waters.johann@hotmail.com', (string)$response->getBody());
        // one of the default admins
        $this->assertNotContains('denesik.stewart@gmail.com', (string)$response->getBody());
    }
}
