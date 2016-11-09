<?php namespace Tests;

use App\Http\Controllers\UsersController;
use Doctrine\DBAL\Connection;
use Limoncello\ContainerLight\Container;
use Limoncello\Testing\PhpUnitTestCase;
use Limoncello\Testing\Sapi;
use Mockery;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\EmitterInterface;

/**
 * @package Tests
 */
class TestCase extends PhpUnitTestCase
{
    /** DateTime format string */
    const JSON_API_DATE_TIME_FORMAT = 'Y-m-d\TH:i:sO';

    /** Header name */
    const HEADER_ORIGIN = 'ORIGIN';

    /**
     * @var AppWrapper|null
     */
    private $appWrapper;

    /**
     * @var bool
     */
    private $shouldPreventCommits = false;

    /**
     * Database connection shared during test when commit prevention is requested.
     *
     * @var Connection|null
     */
    private $sharedConnection = null;

    protected function setUp()
    {
        parent::setUp();

        $this->shouldPreventCommits = false;
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        if ($this->shouldPreventCommits === true && $this->sharedConnection !== null) {
            $this->sharedConnection->rollBack();
            $this->sharedConnection = null;
        }

        Mockery::close();
    }

    /**
     * Prevent commits to database within current test.
     *
     * @return void
     */
    protected function setPreventCommits()
    {
        $this->shouldPreventCommits = true;
    }

    /**
     * Returns database connection used used by application within current test. Needs 'prevent commits' to be set.
     *
     * @return Connection|null
     */
    protected function getCapturedConnection()
    {
        return $this->sharedConnection;
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
        $this->appWrapper = new AppWrapper();

        if ($this->shouldPreventCommits === true) {
            $this->appWrapper->addEventHandler(AppWrapper::EVENT_ON_CONTAINER_LAST_CONFIGURATOR, function (
                AppWrapper $appWrapper,
                Container $container
            ) {
                $appWrapper ?: null;
                if ($this->sharedConnection === null) {
                    // first connection during current test
                    // * other option is take container from function args
                    $this->sharedConnection = $container->get(Connection::class);
                    $this->sharedConnection->beginTransaction();
                } else {
                    // we already have an open connection with transaction started
                    $container[Connection::class] = $this->sharedConnection;
                }
            });
        }

        $this->appWrapper->setSapi($sapi);

        return $this->appWrapper;
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    protected function call(
        $method,
        $uri,
        array $queryParams = [],
        array $parsedBody = [],
        array $headers = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = 'php://input',
        $host = 'localhost'
    ) {
        $headers[static::HEADER_ORIGIN] = $host;

        return parent::call(
            $method,
            $uri,
            $queryParams,
            $parsedBody,
            $headers,
            $cookies,
            $files,
            $server,
            $content,
            $host
        );
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
        return $this->call('POST', $uri, [], [], $headers, $cookies, [], [], $this->streamFromString($json));
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
        return $this->call('PUT', $uri, [], [], $headers, $cookies, [], [], $this->streamFromString($json));
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
        return $this->call('PATCH', $uri, [], [], $headers, $cookies, [], [], $this->streamFromString($json));
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
