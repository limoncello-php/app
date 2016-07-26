<?php namespace Tests;

use App\Application;
use App\Http\Controllers\UsersController;
use Limoncello\Testing\PhpUnitTestCase;
use Limoncello\Testing\Sapi;
use Mockery;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\Utils\TestingConnection;
use Zend\Diactoros\Response\EmitterInterface;

/**
 * @package Tests
 */
class TestCase extends PhpUnitTestCase
{
    /** DateTime format string */
    const JSON_API_DATE_TIME_FORMAT = 'Y-m-d\TH:i:sO';

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        TestingConnection::reset();
        Mockery::close();
    }

    /**
     * Prevent commits to database within current test.
     *
     * @return void
     */
    protected function setPreventCommits()
    {
        TestingConnection::setPreventCommits();
    }

    /**
     * Returns database connection used used by application within current test. Needs 'prevent commits' to be set.
     *
     * @return \Doctrine\DBAL\Connection|null
     */
    protected function getCapturedConnection()
    {
        return TestingConnection::getCapturedConnection();
    }

    /**
     * @inheritdoc
     */
    protected function createSapi(
        array $server = null,
        array $queryParams = null,
        array $parsedBody = null,
        array $cookies = null,
        array $files = null,
        $messageBody = 'php://input'
    ) {
        /** @var EmitterInterface $sapiEmitter */
        $sapiEmitter = Mockery::mock(EmitterInterface::class);

        $sapi = new Sapi($sapiEmitter, $server, $queryParams, $parsedBody, $cookies, $files, $messageBody);

        return $sapi;
    }

    /**
     * @inheritdoc
     */
    protected function createApplication(Sapi $sapi)
    {
        $app = (new Application())->setSapi($sapi);

        return $app;
    }

    /**
     * @param string $uri
     * @param string $json
     * @param array  $headers
     * @param array  $cookies
     *
     * @return ResponseInterface
     */
    protected function postJson($uri, $json, array $headers = [], array $cookies = [])
    {
        $headers[self::HEADER_CONTENT_TYPE] = MediaTypeInterface::JSON_API_MEDIA_TYPE;
        return parent::call('POST', $uri, [], [], $headers, $cookies, [], [], $this->streamFromString($json));
    }

    /**
     * @param string $uri
     * @param string $json
     * @param array  $headers
     * @param array  $cookies
     *
     * @return ResponseInterface
     */
    protected function putJson($uri, $json, array $headers = [], array $cookies = [])
    {
        $headers[self::HEADER_CONTENT_TYPE] = MediaTypeInterface::JSON_API_MEDIA_TYPE;
        return parent::call('PUT', $uri, [], [], $headers, $cookies, [], [], $this->streamFromString($json));
    }

    /**
     * @param string $uri
     * @param string $json
     * @param array  $headers
     * @param array  $cookies
     *
     * @return ResponseInterface
     */
    protected function patchJson($uri, $json, array $headers = [], array $cookies = [])
    {
        $headers[self::HEADER_CONTENT_TYPE] = MediaTypeInterface::JSON_API_MEDIA_TYPE;
        return parent::call('PATCH', $uri, [], [], $headers, $cookies, [], [], $this->streamFromString($json));
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return string
     */
    protected function setUserToken($email, $password)
    {
        $formData = [
            UsersController::FORM_EMAIL    => $email,
            UsersController::FORM_PASSWORD => $password,
        ];
        $response = $this->post('/authenticate', $formData);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($token = (string)$response->getBody());

        return $token;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getAuthorizationHeaders($token)
    {
        return ['Authorization' => 'Bearer ' . $token];
    }

    /**
     * @return array
     */
    protected function createAdminAuthHeaders()
    {
        return $this->getAuthorizationHeaders($this->setUserToken('admin@admins.tld', 'password'));
    }

    /**
     * @return array
     */
    protected function createUserAuthHeaders()
    {
        return $this->getAuthorizationHeaders($this->setUserToken('user@users.tld', 'password'));
    }
}
