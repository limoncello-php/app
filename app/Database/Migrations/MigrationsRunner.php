<?php namespace App\Database\Migrations;

use Doctrine\DBAL\Schema\AbstractSchemaManager;

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
     * @param AbstractSchemaManager $schemaManager
     *
     * @return void
     */
    public function migrate(AbstractSchemaManager $schemaManager)
    {
        foreach ($this->migrations as $class) {
            /** @var Migration $migration */
            $migration = new $class($schemaManager);
            $migration->migrate();
        }
    }

    /**
     * @param AbstractSchemaManager $schemaManager
     *
     * @return void
     */
    public function rollback(AbstractSchemaManager $schemaManager)
    {
        foreach (array_reverse($this->migrations, false) as $class) {
            /** @var Migration $migration */
            $migration = new $class($schemaManager);
            $migration->rollback();
        }
    }
}
