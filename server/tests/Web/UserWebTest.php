<?php namespace Tests\Web;

use App\Data\Seeds\RolesSeed;
use App\Json\Schemes\UserSchema;
use Tests\TestCase;

/**
 * @package Tests
 */
class UserWebTest extends TestCase
{
    const RESOURCES_URL = '/users';

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
                UserSchema::ATTR_FIRST_NAME => [
                    'like' => '%m%',
                ],
                UserSchema::ATTR_CREATED_AT => [
                    'less-than' => '2100-02-03T04:05:06+0000',
                ],
            ],
        ];

        // execution time measurement example
        $response = $this->get(self::RESOURCES_URL, $queryParams, [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());

        $body = (string)$response->getBody();
        $this->assertNotContains('Layla', $body);
        $this->assertContains('Benjamin', $body);
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
                'limit'  => '2',
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
     * Test index page.
     *
     * @return void
     */
    public function testIndexAllowedForUsers(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getPlainUserOAuthCookie();

        $response = $this->get(static::RESOURCES_URL, [], [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test open edit user form.
     *
     * @return void
     */
    public function testOpenEditForm(): void
    {
        $userId = 3;

        $this->setPreventCommits();

        $authCookie = $this->getModeratorOAuthCookie();

        $response = $this->get(self::RESOURCES_URL . '/' . $userId, [], [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotContains('Create', (string)$response->getBody());
        $this->assertContains('Update', (string)$response->getBody());
        $this->assertContains('Delete', (string)$response->getBody());
    }

    /**
     * Test open edit user form.
     *
     * @return void
     */
    public function testOpenEditFormForbiddenForPlainUsers(): void
    {
        $userId = 3;

        $this->setPreventCommits();

        $authCookie = $this->getPlainUserOAuthCookie();

        $response = $this->get(self::RESOURCES_URL . '/' . $userId, [], [], $authCookie);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test open create user form.
     *
     * @return void
     */
    public function testOpenCreateForm(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getModeratorOAuthCookie();

        $response = $this->get(self::RESOURCES_URL . '/create', [], [], $authCookie);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Create', (string)$response->getBody());
        $this->assertNotContains('Delete', (string)$response->getBody());
        $this->assertNotContains('Update', (string)$response->getBody());
    }

    /**
     * Test open create user form.
     *
     * @return void
     */
    public function testOpenCreateFormForbiddenForPlainUsers(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getPlainUserOAuthCookie();

        $response = $this->get(self::RESOURCES_URL . '/create', [], [], $authCookie);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test post 'create user' form data.
     *
     * @return void
     */
    public function testPostCreateFormWithValidData(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getModeratorOAuthCookie();

        $data = [
            UserSchema::ATTR_FIRST_NAME              => 'John',
            UserSchema::ATTR_LAST_NAME               => 'Doe',
            UserSchema::ATTR_EMAIL                   => 'john@doe.foo',
            UserSchema::REL_ROLE                     => RolesSeed::ROLE_USER,
            UserSchema::V_ATTR_PASSWORD              => '123456',
            UserSchema::V_ATTR_PASSWORD_CONFIRMATION => '123456',
        ];

        $response = $this->post(self::RESOURCES_URL . '/create', $data, [], $authCookie);

        // check we've got redirected on success
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test post 'create user' form data.
     *
     * @return void
     */
    public function testPostCreateFormWithInvalidData(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getModeratorOAuthCookie();

        $data = [
            UserSchema::ATTR_FIRST_NAME              => 'John',
            UserSchema::ATTR_LAST_NAME               => 'Doe',
            UserSchema::ATTR_EMAIL                   => 'john@doe.foo',
            UserSchema::REL_ROLE                     => RolesSeed::ROLE_USER,
            UserSchema::V_ATTR_PASSWORD              => '123456',
            UserSchema::V_ATTR_PASSWORD_CONFIRMATION => '123456' . 'XXX', // <-- passwords do not match
        ];

        $response = $this->post(self::RESOURCES_URL . '/create', $data, [], $authCookie);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertContains('Passwords should match', (string)$response->getBody());
    }

    /**
     * Test post 'update user' form data.
     *
     * @return void
     */
    public function testPostUpdateFormWithValidData(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getModeratorOAuthCookie();

        $data = [
            UserSchema::ATTR_FIRST_NAME              => 'John',
            UserSchema::ATTR_LAST_NAME               => 'Doe',
            UserSchema::ATTR_EMAIL                   => 'john@doe.foo',
            UserSchema::REL_ROLE                     => RolesSeed::ROLE_USER,
            UserSchema::V_ATTR_PASSWORD              => '', // empty passwords would be ignored
            UserSchema::V_ATTR_PASSWORD_CONFIRMATION => '', // empty passwords would be ignored
        ];

        $userId   = 2;
        $response = $this->post(self::RESOURCES_URL . "/$userId", $data, [], $authCookie);

        // check we've got redirected on success
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test post 'update user' form data.
     *
     * @return void
     */
    public function testPostUpdateFormWithInvalidData(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getModeratorOAuthCookie();

        $data = [
            UserSchema::ATTR_FIRST_NAME              => 'John',
            UserSchema::ATTR_LAST_NAME               => 'Doe',
            UserSchema::ATTR_EMAIL                   => 'john@doe.foo',
            UserSchema::REL_ROLE                     => RolesSeed::ROLE_USER,
            UserSchema::V_ATTR_PASSWORD              => '123', // too short password
            UserSchema::V_ATTR_PASSWORD_CONFIRMATION => '123', // too short password
        ];

        $userId   = 2;
        $response = $this->post(self::RESOURCES_URL . "/$userId", $data, [], $authCookie);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertContains('at least 5 characters', (string)$response->getBody());
    }

    /**
     * Test delete user.
     *
     * @return void
     */
    public function testDeleteUser(): void
    {
        $this->setPreventCommits();

        $authCookie = $this->getModeratorOAuthCookie();

        $userId   = 2;
        $response = $this->post(self::RESOURCES_URL . "/$userId/delete", [], [], $authCookie);

        // check we've got redirected on success
        $this->assertEquals(302, $response->getStatusCode());
    }
}
