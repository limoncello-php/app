<?php namespace App\Database\Migrations;

use Doctrine\DBAL\Connection;
use Interop\Container\ContainerInterface;

/**
 * @package App
 */
abstract class Migration
{
    /**
     * @return void
     */
    abstract public function migrate();

    /**
     * @return void
     */
    abstract public function rollback();

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->getContainer()->get(Connection::class);
    }
}
