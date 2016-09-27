<?php namespace Tests;

use App\Application;
use Closure;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;

/**
 * This is wrapper for the application which adds additional features such as events.
 *
 * @package Tests
 */
class AppWrapper extends Application
{
    /** Called right before request will be passed through all middleware, controller and back */
    const EVENT_ON_HANDLE_REQUEST = 0;

    /** Called when response has passed back all middleware right before sending to client */
    const EVENT_ON_HANDLE_RESPONSE = self::EVENT_ON_HANDLE_REQUEST + 1;

    /** Called on empty container created (before it's set up) */
    const EVENT_ON_CONTAINER_CREATED = self::EVENT_ON_HANDLE_RESPONSE + 1;

    /** Called right before controller is called */
    const EVENT_ON_CONTAINER_LAST_CONFIGURATOR = self::EVENT_ON_CONTAINER_CREATED + 1;

    /**
     * @var array
     */
    private $events = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param int     $eventId
     * @param Closure $handler
     *
     * @return void
     */
    public function addEventHandler($eventId, Closure $handler)
    {
        $this->events[$eventId][] = $handler;
    }

    /**
     * @inheritdoc
     */
    protected function configureUserContainer(ContainerInterface $container, array $configurators)
    {
        parent::configureUserContainer($container, $configurators);

        $this->dispatchEvent(self::EVENT_ON_CONTAINER_LAST_CONFIGURATOR, [$container]);
    }

    /**
     * @inheritdoc
     */
    protected function handleRequest(Closure $handler, RequestInterface $request = null)
    {
        $this->dispatchEvent(self::EVENT_ON_HANDLE_REQUEST, [$request]);

        $response = parent::handleRequest($handler, $request);

        $this->dispatchEvent(self::EVENT_ON_HANDLE_REQUEST, [$response]);

        return $response;
    }

    /**
     * @inheritdoc
     */
    protected function createContainer()
    {
        $this->container = parent::createContainer();

        $this->dispatchEvent(self::EVENT_ON_CONTAINER_CREATED, [$this->container]);

        return $this->container;
    }

    /**
     * @param int   $eventId
     * @param array $arguments
     *
     * @return void
     */
    private function dispatchEvent($eventId, array $arguments)
    {
        $handlers   = $this->getEventHandlers($eventId);
        $appAndArgs = array_merge([$this], $arguments);
        foreach ($handlers as $handler) {
            call_user_func_array($handler, $appAndArgs);
        }
    }

    /**
     * @param int $eventId
     *
     * @return Closure[]
     */
    private function getEventHandlers($eventId)
    {
        return array_key_exists($eventId, $this->events) === true ? $this->events[$eventId] : [];
    }
}
