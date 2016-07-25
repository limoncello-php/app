<?php namespace App\Commands;

use App\Container\SetUpConfig;
use App\Container\SetUpDatabase;
use App\Database\Migrations\MigrationsRunner;
use App\Database\Seeds\SeedsRunner;
use Composer\Script\Event;
use Doctrine\DBAL\Connection;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 */
class Database
{
    use SetUpConfig, SetUpDatabase;

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function reset(Event $event)
    {
        (new MigrationsRunner())->rollback(static::getConnection()->getSchemaManager());

        $event->getIO()->write("<info>Database reset completed.</info>");
    }

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function migrate(Event $event)
    {
        (new MigrationsRunner())->migrate(static::getConnection()->getSchemaManager());

        $event->getIO()->write("<info>Database migration completed.</info>");
    }

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function seed(Event $event)
    {
        (new SeedsRunner())->run(static::getConnection());

        $event->getIO()->write("<info>Database seeding completed.</info>");
    }

    /**
     * @return Connection
     */
    protected static function getConnection()
    {
        $container = new Container();
        static::setUpConfig($container);
        static::setUpDatabase($container);

        /** @var Connection $pdo */
        $pdo = $container->get(Connection::class);

        return $pdo;
    }
}
