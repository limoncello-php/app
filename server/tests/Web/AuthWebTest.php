<?php namespace Tests\Web;

use App\Web\Controllers\AuthController;
use App\Web\Middleware\CookieAuth;
use DateTime;
use Limoncello\Application\Packages\Csrf\CsrfSettings;
use Limoncello\Contracts\Cookies\CookieJarInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\OAuthSignInTrait;
use Tests\TestCase;

/**
 * @package Tests
 */
class AuthWebTest extends TestCase
{
    use OAuthSignInTrait;

    const SIGN_IN_URL = '/sign-in';

    const SIGN_OUT_URL = '/sign-out';

    /**
     * Test show sign-in page.
     */
    public function testIndex(): void
    {
        $response = $this->get(self::SIGN_IN_URL);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test valid sign-in.
     *
     * @return void
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testSignInWithValidCredentials(): void
    {
        $this->setPreventCommits();

        // add to session CSRF token(s) like it was issued by the server before.
        $this->setSessionCsrfTokens(['secret_token']);

        $form = [
            AuthController::FORM_EMAIL    => $this->getUserEmail(),
            AuthController::FORM_PASSWORD => $this->getUserPassword(),
            AuthController::FORM_REMEMBER => 'on',

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'secret_token',
        ];

        $response = $this->post(self::SIGN_IN_URL, $form);

        $this->assertEquals(302, $response->getStatusCode());

        // check auth cookie was set
        $nowAsTimestamp = (new DateTime())->getTimestamp();

        /** @var CookieJarInterface $cookies */
        $cookies = $this->getAppContainer()->get(CookieJarInterface::class);
        $this->assertTrue($cookies->has(CookieAuth::COOKIE_NAME));
        $this->assertTrue($nowAsTimestamp < $cookies->get(CookieAuth::COOKIE_NAME)->getExpiresAtUnixTime());
    }

    /**
     * Test invalid sign-in.
     *
     * @return void
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testSignInWithInvalidCredentials(): void
    {
        $this->setPreventCommits();

        // add to session CSRF token(s) like it was issued by the server before.
        $this->setSessionCsrfTokens(['secret_token']);

        $form = [
            AuthController::FORM_EMAIL    => $this->getUserEmail(),
            AuthController::FORM_PASSWORD => $this->getUserPassword() . '-=#', // <- invalid password

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'secret_token',
        ];

        $response = $this->post(self::SIGN_IN_URL, $form);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertContains('Invalid email or password', (string)$response->getBody());

        // check auth cookie was not set

        /** @var CookieJarInterface $cookies */
        $cookies = $this->getAppContainer()->get(CookieJarInterface::class);
        $this->assertFalse($cookies->has(CookieAuth::COOKIE_NAME));
    }

    /**
     * Test invalid sign-in.
     *
     * @return void
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testSignInWithInvalidInputs(): void
    {
        $this->setPreventCommits();

        // add to session CSRF token(s) like it was issued by the server before.
        $this->setSessionCsrfTokens(['secret_token']);

        $form = [
            AuthController::FORM_EMAIL    => 'it-does-not-look-like-email',
            AuthController::FORM_PASSWORD => '123', // <- too short for a password

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'secret_token',
        ];

        $response = $this->post(self::SIGN_IN_URL, $form);

        $this->assertEquals(422, $response->getStatusCode());

        // check auth cookie was not set

        /** @var CookieJarInterface $cookies */
        $cookies = $this->getAppContainer()->get(CookieJarInterface::class);
        $this->assertFalse($cookies->has(CookieAuth::COOKIE_NAME));
    }

    /**
     * Test logout page.
     *
     * @return void
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testLogout(): void
    {
        $response = $this->get(self::SIGN_OUT_URL);

        $this->assertEquals(302, $response->getStatusCode());

        // check auth cookie was set
        $nowAsTimestamp = (new DateTime())->getTimestamp();

        /** @var CookieJarInterface $cookies */
        $cookies = $this->getAppContainer()->get(CookieJarInterface::class);
        $this->assertTrue($cookies->has(CookieAuth::COOKIE_NAME));
        $this->assertTrue($cookies->get(CookieAuth::COOKIE_NAME)->getExpiresAtUnixTime() < $nowAsTimestamp);
    }
}
