<?php namespace Tests;

use App\Authentication\CookieAuth;
use App\Data\Seeds\UsersSeed;
use Psr\Http\Message\ResponseInterface;

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
        return $this->getOAuthHeader($this->getAdminOAuthToken());
    }

    /**
     * @return array
     */
    protected function getModeratorOAuthHeader(): array
    {
        return $this->getOAuthHeader($this->getModeratorOAuthToken());
    }

    /**
     * @return array
     */
    protected function getPlainUserOAuthHeader(): array
    {
        return $this->getOAuthHeader($this->getUserOAuthToken());
    }

    /**
     * @return array
     */
    protected function getAdminOAuthCookie(): array
    {
        return $this->getOAuthCookie($this->getAdminOAuthToken());
    }

    /**
     * @return array
     */
    protected function getModeratorOAuthCookie(): array
    {
        return $this->getOAuthCookie($this->getModeratorOAuthToken());
    }

    /**
     * @return array
     */
    protected function getPlainUserOAuthCookie(): array
    {
        return $this->getOAuthCookie($this->getUserOAuthToken());
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
     * @return string
     */
    private function getAdminOAuthToken(): string
    {
        return $this->requestOAuthToken($this->getAdminEmail(), $this->getAdminPassword());
    }

    /**
     * @return string
     */
    private function getModeratorOAuthToken(): string
    {
        return $this->requestOAuthToken($this->getModeratorEmail(), $this->getModeratorPassword());
    }

    /**
     * @return string
     */
    private function getUserOAuthToken(): string
    {
        return $this->requestOAuthToken($this->getUserEmail(), $this->getUserPassword());
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return string
     */
    private function requestOAuthToken(string $username, string $password): string
    {
        /** @var ResponseInterface $response */
        $response = $this->post('/token', [
            'grant_type' => 'password',
            'username'   => $username,
            'password'   => $password,
        ]);

        assert($response->getStatusCode() == 200);
        assert(($token = json_decode((string)$response->getBody())) !== false);

        $value = $token->access_token;
        assert(empty($value) === false);

        return $value;
    }
}
