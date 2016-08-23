<?php namespace App\Commands;

use App\Container\SetUpConfig;
use App\Container\SetUpCrypt;
use App\Container\SetUpDatabase;
use App\Container\SetUpLogs;
use App\Database\MigrationsRunner;
use App\Database\SeedsRunner;
use Composer\Script\Event;
use Doctrine\DBAL\Connection;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 */
class Database
{
    use SetUpConfig, SetUpCrypt, SetUpDatabase, SetUpLogs;

    /**
     * @var null|Container
     */
    private static $container = null;

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function reset(Event $event)
    {
        (new MigrationsRunner())->rollback(static::getContainer());

        $event->getIO()->write("<info>Database reset completed.</info>");
    }

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function migrate(Event $event)
    {
        (new MigrationsRunner())->migrate(static::getContainer());

        $event->getIO()->write("<info>Database migration completed.</info>");
    }

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function seed(Event $event)
    {
        (new SeedsRunner())->run(static::getContainer());

        $event->getIO()->write("<info>Database seeding completed.</info>");
    }

    /**
     * @return Connection
     */
    protected static function getConnection()
    {
        $connection = static::getContainer()->get(Connection::class);

        return $connection;
    }

    /**
     * @return Container
     */
    protected static function getContainer()
    {
        static::initVariables();

        return static::$container;
    }

    /**
     * Init class variables.
     */
    private static function initVariables()
    {
        if (static::$container === null) {
            $container = new Container();

            static::setUpConfig($container);
            static::setUpCrypt($container);
            static::setUpDatabase($container);
            static::setUpFileLogs($container);

            static::$container = $container;
        }
    }
}
