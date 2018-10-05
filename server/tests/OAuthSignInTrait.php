<?php namespace Tests;

use App\Data\Seeds\UsersSeed;
use App\Web\Middleware\CookieAuth;
use Closure;
use Limoncello\Contracts\Core\ApplicationInterface;
use Limoncello\Contracts\Passport\PassportAccountManagerInterface;
use Limoncello\Passport\Contracts\PassportServerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\ServerRequest;

/**
 * @package Tests
 *
 * @method ResponseInterface post(string $url, array $data)
 */
trait OAuthSignInTrait
{
    /**
     *
     * @return string
     */
    private function getUserEmail(): string
    {
        return 'bettie14@gmail.com';
    }

    /**
     *
     * @return string
     */
    private function getUserPassword(): string
    {
        return UsersSeed::DEFAULT_PASSWORD;
    }

    /**
     *
     * @return string
     */
    private function getModeratorEmail(): string
    {
        return 'waters.johann@hotmail.com';
    }

    /**
     *
     * @return string
     */
    private function getModeratorPassword(): string
    {
        return UsersSeed::DEFAULT_PASSWORD;
    }

    /**
     *
     * @return string
     */
    private function getAdminEmail(): string
    {
        return 'denesik.stewart@gmail.com';
    }

    /**
     *
     * @return string
     */
    private function getAdminPassword(): string
    {
        return UsersSeed::DEFAULT_PASSWORD;
    }

    /**
     * @return array
     */
    protected function getAdminOAuthHeader(): array
    {
        return $this->getOAuthHeader($this->extractOAuthAccessTokenValue($this->getAdminOAuthToken()));
    }

    /**
     * @return array
     */
    protected function getModeratorOAuthHeader(): array
    {
        return $this->getOAuthHeader($this->extractOAuthAccessTokenValue($this->getModeratorOAuthToken()));
    }

    /**
     * @return array
     */
    protected function getPlainUserOAuthHeader(): array
    {
        return $this->getOAuthHeader($this->extractOAuthAccessTokenValue($this->getUserOAuthToken()));
    }

    /**
     * @return array
     */
    protected function getAdminOAuthCookie(): array
    {
        return $this->getOAuthCookie($this->extractOAuthAccessTokenValue($this->getAdminOAuthToken()));
    }

    /**
     * @return array
     */
    protected function getModeratorOAuthCookie(): array
    {
        return $this->getOAuthCookie($this->extractOAuthAccessTokenValue($this->getModeratorOAuthToken()));
    }

    /**
     * @return array
     */
    protected function getPlainUserOAuthCookie(): array
    {
        return $this->getOAuthCookie($this->extractOAuthAccessTokenValue($this->getUserOAuthToken()));
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getOAuthHeader(string $token): array
    {
        return ['Authorization' => 'Bearer ' . $token];
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getOAuthCookie(string $token): array
    {
        return [CookieAuth::COOKIE_NAME => $token];
    }

    /**
     * @return object
     */
    protected function getAdminOAuthToken()
    {
        return $this->getOAuthToken($this->getAdminEmail(), $this->getAdminPassword());
    }

    /**
     * @return object
     */
    protected function getModeratorOAuthToken()
    {
        return $this->getOAuthToken($this->getModeratorEmail(), $this->getModeratorPassword());
    }

    /**
     * @return object
     */
    protected function getUserOAuthToken()
    {
        return $this->getOAuthToken($this->getUserEmail(), $this->getUserPassword());
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return object
     */
    private function getOAuthToken(string $username, string $password)
    {
        /** @var ResponseInterface $response */
        $response = $this->post('/token', $this->createOAuthTokenRequestBody($username, $password));

        assert($response->getStatusCode() == 200);
        assert(($token = json_decode((string)$response->getBody())) !== false);

        return $token;
    }

    /**
     * @param object $token
     *
     * @return string
     */
    private function extractOAuthAccessTokenValue($token): string
    {
        assert(is_object($token));
        assert(isset($token->access_token));
        $value = $token->access_token;
        assert(empty($value) === false);

        return $value;
    }

    /**
     * @return Closure
     */
    private function createSetAdminAccount(): Closure
    {
        return $this->createSetUserClosure($this->getAdminEmail(), $this->getAdminPassword());
    }

    /**
     * @return Closure
     */
    private function createSetModeratorAccount(): Closure
    {
        return $this->createSetUserClosure($this->getModeratorEmail(), $this->getModeratorPassword());
    }

    /**
     * @return Closure
     */
    private function createSetUserAccount(): Closure
    {
        return $this->createSetUserClosure($this->getUserEmail(), $this->getUserPassword());
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return Closure
     */
    private function createSetUserClosure(string $username, string $password): Closure
    {
        return function (ApplicationInterface $app, ContainerInterface $container) use ($username, $password): void
        {
            assert($app !== null);

            /** @var PassportAccountManagerInterface $manager */
            assert($container->has(PassportAccountManagerInterface::class));
            $manager = $container->get(PassportAccountManagerInterface::class);

            $request = (new ServerRequest())->withParsedBody($this->createOAuthTokenRequestBody($username, $password));

            /** @var PassportServerInterface $passportServer */
            $passportServer = $container->get(PassportServerInterface::class);
            $tokenResponse  = $passportServer->postCreateToken($request);
            assert($tokenResponse->getStatusCode() === 200);
            $token          = json_decode((string)$tokenResponse->getBody());
            $authToken      = $token->access_token;

            $manager->setAccountWithTokenValue($authToken);
        };
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return array
     */
    private function createOAuthTokenRequestBody(string $username, string $password): array
    {
        return [
            'grant_type' => 'password',
            'username'   => $username,
            'password'   => $password,
        ];
    }
}
