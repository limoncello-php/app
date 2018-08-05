<?php namespace Tests\Web;

use App\Web\Controllers\AuthController;
use App\Web\Middleware\CookieAuth;
use DateTime;
use Limoncello\Application\Packages\Csrf\CsrfSettings;
use Limoncello\Contracts\Cookies\CookieJarInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\OAuthSignInTrait;
use Tests\TestCase;

/**
 * @package Tests
 */
class HomeWebTest extends TestCase
{
    use OAuthSignInTrait;

    const PAGE_URL = '/';

    const SIGN_IN_URL = '/sign-in';

    const SIGN_OUT_URL = '/sign-out';

    /**
     * Test show home page.
     */
    public function testIndex(): void
    {
        // execution time measurement example
        $response = $this->measureTime(function (): ResponseInterface {
            return $this->get(self::PAGE_URL);
        }, $time);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertLessThan(1.0, $time, 'Our home page has become sloppy.');
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

        // make CSRF protection check successful
        $this->passThroughCsrfOnNextAppCall();

        $form = [
            AuthController::FORM_EMAIL    => $this->getUserEmail(),
            AuthController::FORM_PASSWORD => $this->getUserPassword(),
            AuthController::FORM_REMEMBER => 'on',

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'anything',
        ];

        $this->captureFromNextAppCall(CookieJarInterface::class);

        $response = $this->post(self::SIGN_IN_URL, $form);

        /** @var CookieJarInterface $cookies */
        $cookies = $this->getCapturedFromPreviousAppCall(CookieJarInterface::class);

        $this->assertEquals(302, $response->getStatusCode());

        // check auth cookie was set
        $nowAsTimestamp = (new DateTime())->getTimestamp();

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

        // make CSRF protection check successful
        $this->passThroughCsrfOnNextAppCall();

        $form = [
            AuthController::FORM_EMAIL    => $this->getUserEmail(),
            AuthController::FORM_PASSWORD => $this->getUserPassword() . '-=#', // <- invalid password

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'anything',
        ];

        $this->captureFromNextAppCall(CookieJarInterface::class);

        $response = $this->post(self::SIGN_IN_URL, $form);

        /** @var CookieJarInterface $cookies */
        $cookies = $this->getCapturedFromPreviousAppCall(CookieJarInterface::class);

        $this->assertEquals(401, $response->getStatusCode());

        // check auth cookie was not set

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

        // make CSRF protection check successful
        $this->passThroughCsrfOnNextAppCall();

        $form = [
            AuthController::FORM_EMAIL    => 'it-does-not-look-like-email',
            AuthController::FORM_PASSWORD => '123', // <- too short for a password

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => 'anything',
        ];

        $this->captureFromNextAppCall(CookieJarInterface::class);

        $response = $this->post(self::SIGN_IN_URL, $form);

        /** @var CookieJarInterface $cookies */
        $cookies = $this->getCapturedFromPreviousAppCall(CookieJarInterface::class);

        $this->assertEquals(422, $response->getStatusCode());

        // check auth cookie was not set
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
        $this->captureFromNextAppCall(CookieJarInterface::class);

        $response = $this->get(self::SIGN_OUT_URL);

        /** @var CookieJarInterface $cookies */
        $cookies = $this->getCapturedFromPreviousAppCall(CookieJarInterface::class);

        $this->assertEquals(302, $response->getStatusCode());

        // check auth cookie was set
        $nowAsTimestamp = (new DateTime())->getTimestamp();

        $this->assertTrue($cookies->has(CookieAuth::COOKIE_NAME));
        $this->assertTrue($cookies->get(CookieAuth::COOKIE_NAME)->getExpiresAtUnixTime() < $nowAsTimestamp);
    }
}
