<?php namespace Tests;

use App\Application;
use App\Data\Seeds\UsersSeed;
use Doctrine\DBAL\Connection;
use Limoncello\Contracts\Container\ContainerInterface;
use Limoncello\Contracts\Core\ApplicationInterface;
use Limoncello\Testing\ApplicationWrapperInterface;
use Limoncello\Testing\ApplicationWrapperTrait;
use Limoncello\Testing\HttpCallsTrait;
use Limoncello\Testing\MeasureExecutionTimeTrait;
use Limoncello\Testing\Sapi;
use Limoncello\Testing\TestCaseTrait;
use Mockery;
use Zend\Diactoros\Response\EmitterInterface;

/**
 * @package Tests
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    use TestCaseTrait, HttpCallsTrait, MeasureExecutionTimeTrait;

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

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->resetEventHandlers();

        $this->sharedConnection     = null;
        $this->shouldPreventCommits = false;
        $interceptConnection        = function (ApplicationWrapperInterface $wrapper, ContainerInterface $container) {
            assert($wrapper);
            if ($this->shouldPreventCommits === true) {
                if ($this->sharedConnection === null) {
                    // first connection during current test
                    // * other option is take container from function args
                    $this->sharedConnection = $container->get(Connection::class);
                    $this->sharedConnection->beginTransaction();
                } else {
                    // we already have an open connection with transaction started
                    $container[Connection::class] = $this->sharedConnection;
                }
            }
        };
        $this->addOnContainerConfiguredEvent($interceptConnection);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        if ($this->shouldPreventCommits === true && $this->sharedConnection !== null) {
            $this->sharedConnection->rollBack();
        }
        $this->sharedConnection     = null;
        $this->shouldPreventCommits = false;
        $this->resetEventHandlers();

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
    protected function createApplication(): ApplicationInterface
    {
        $wrapper = new class extends Application implements ApplicationWrapperInterface {
            use ApplicationWrapperTrait;
        };

        foreach ($this->getHandleRequestEvents() as $handler) {
            $wrapper->addOnHandleRequest($handler);
        }

        foreach ($this->getHandleResponseEvents() as $handler) {
            $wrapper->addOnHandleResponse($handler);
        }

        foreach ($this->getContainerCreatedEvents() as $handler) {
            $wrapper->addOnContainerCreated($handler);
        }

        foreach ($this->getContainerConfiguredEvents() as $handler) {
            $wrapper->addOnContainerLastConfigurator($handler);
        }

        return $wrapper;
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
        $messageBody = 'php://input',
        string $protocolVersion = '1.1'
    ): Sapi {
        /** @var EmitterInterface $emitter */
        $emitter = Mockery::mock(EmitterInterface::class);

        $sapi =
            new Sapi($emitter, $server, $queryParams, $parsedBody, $cookies, $files, $messageBody, $protocolVersion);

        return $sapi;
    }

    /**
     * @return string
     */
    protected function getAdminOAuthToken(): string
    {
        $response = $this->post('/token', [
            'grant_type' => 'password',
            'username'   => 'kurt.murray@berge.biz',
            'password'   => UsersSeed::DEFAULT_PASSWORD,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEquals(false, $token = json_decode((string)$response->getBody()));
        $this->assertNotEmpty($token = $token->access_token);

        return $token;
    }

    /**
     * @return string
     */
    protected function getModeratorOAuthToken(): string
    {
        $response = $this->post('/token', [
            'grant_type' => 'password',
            'username'   => 'ybins@yahoo.com',
            'password'   => UsersSeed::DEFAULT_PASSWORD,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEquals(false, $token = json_decode((string)$response->getBody()));
        $this->assertNotEmpty($token = $token->access_token);

        return $token;
    }

    /**
     * @return string
     */
    protected function getPlainUserOAuthToken(): string
    {
        $response = $this->post('/token', [
            'grant_type' => 'password',
            'username'   => 'denesik.stewart@gmail.com',
            'password'   => UsersSeed::DEFAULT_PASSWORD,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEquals(false, $token = json_decode((string)$response->getBody()));
        $this->assertNotEmpty($token = $token->access_token);

        return $token;
    }
}
