<?php namespace Tests;

use App\Data\Seeds\UsersSeed;
use Psr\Http\Message\ResponseInterface;

/**
 * @package Tests
 *
 * @method ResponseInterface post(string $url, array $data)
 */
trait OAuthLoginTrait
{
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
        return $this->getOAuthHeader($this->getPlainUserOAuthToken());
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
     * @return string
     */
    private function getAdminOAuthToken(): string
    {
        return $this->requestOAuthToken('kurt.murray@berge.biz', UsersSeed::DEFAULT_PASSWORD);
    }

    /**
     * @return string
     */
    private function getModeratorOAuthToken(): string
    {
        return $this->requestOAuthToken('ybins@yahoo.com', UsersSeed::DEFAULT_PASSWORD);
    }

    /**
     * @return string
     */
    private function getPlainUserOAuthToken(): string
    {
        return $this->requestOAuthToken('denesik.stewart@gmail.com', UsersSeed::DEFAULT_PASSWORD);
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
