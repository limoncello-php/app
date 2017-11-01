<?php namespace Tests;

use App\Application;
use Doctrine\DBAL\Connection;
use Limoncello\Application\Contracts\Session\SessionFunctionsInterface;
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
    use TestCaseTrait, HttpCallsTrait, MeasureExecutionTimeTrait, OAuthLoginTrait;

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

        // keep database connection between multiple App call during a single test
        $this->sharedConnection     = null;
        $this->shouldPreventCommits = false;
        $interceptConnection        = function (ApplicationInterface $app, ContainerInterface $container) {
            assert($app);
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

        // in testing environment replace PHP session functions with mocks
        $replaceSessionFunctions = function (ApplicationInterface $app, ContainerInterface $container) {
            assert($app);
            if ($container->has(SessionFunctionsInterface::class) === true) {
                $doNothing = function () {
                };

                /** @var SessionFunctionsInterface $functions */
                $functions = $container->get(SessionFunctionsInterface::class);
                $functions
                    ->setStartCallable($doNothing)
                    ->setWriteCloseCallable($doNothing);
            }
        };
        $this->addOnContainerConfiguredEvent($replaceSessionFunctions);
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
        $wrapper = new class extends Application implements ApplicationWrapperInterface
        {
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
}
