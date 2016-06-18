<?php namespace App\Commands;

use App\Container\SetUpConfig;
use App\Container\SetUpPdo;
use App\Database\Migrations\MigrationsRunner;
use App\Database\Seeds\SeedsRunner;
use Composer\Script\Event;
use Limoncello\ContainerLight\Container;
use PDO;

/**
 * @package App
 */
class Database
{
    use SetUpConfig, SetUpPdo;

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function reset(Event $event)
    {
        (new MigrationsRunner())->rollback(static::getPdo());

        $event->getIO()->write("<info>Database reset completed.</info>");
    }

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function migrate(Event $event)
    {
        (new MigrationsRunner())->migrate(static::getPdo());

        $event->getIO()->write("<info>Database migration completed.</info>");
    }

    /**
     * @param Event $event
     *
     * @return void
     */
    public static function seed(Event $event)
    {
        (new SeedsRunner())->run(static::getPdo());

        $event->getIO()->write("<info>Database seeding completed.</info>");
    }

    /**
     * @return PDO
     */
    protected static function getPdo()
    {
        $container = new Container();
        static::setUpConfig($container);
        static::setUpPdo($container);

        /** @var PDO $pdo */
        $pdo = $container->get(PDO::class);

        return $pdo;
    }
}
