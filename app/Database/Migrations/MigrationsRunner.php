<?php namespace App\Database\Migrations;

use Exception;
use PDO;

/**
 * @package App
 */
class MigrationsRunner
{
    /**
     * @var string[]
     */
    private $migrations = [
        BoardsMigration::class,
        RolesMigration::class,
        UsersMigration::class,
        PostsMigration::class,
        CommentsMigration::class,
    ];

    /**
     * @param PDO $pdo
     *
     * @return void
     *
     * @throws Exception
     */
    public function migrate(PDO $pdo)
    {
        foreach ($this->migrations as $class) {
            try {
                /** @var Migration $migration */
                $migration = new $class();
                $migration->migrate($pdo);
            } catch (Exception $exception) {
                throw new Exception("Migration '$class' failed.", 0, $exception);
            }
        }
    }

    /**
     * @param PDO $pdo
     *
     * @return void
     *
     * @throws Exception
     */
    public function rollback(PDO $pdo)
    {
        foreach (array_reverse($this->migrations, false) as $class) {
            try {
                /** @var Migration $migration */
                $migration = new $class();
                $migration->rollback($pdo);
            } catch (Exception $exception) {
                throw new Exception("Migration '$class' failed.", 0, $exception);
            }
        }
    }
}
