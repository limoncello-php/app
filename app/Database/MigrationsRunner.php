<?php namespace App\Database;

use App\Database\Migrations\BoardsMigration;
use App\Database\Migrations\CommentsMigration;
use App\Database\Migrations\DateTimeFunctionMigration;
use App\Database\Migrations\Migration;
use App\Database\Migrations\PostsMigration;
use App\Database\Migrations\RolesMigration;
use App\Database\Migrations\UsersMigration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Generator;
use Interop\Container\ContainerInterface;

/**
 * @package App
 */
class MigrationsRunner
{
    /**
     * @return string[]
     */
    protected function getMigrationsList()
    {
        return [
            BoardsMigration::class,
            RolesMigration::class,
            UsersMigration::class,
            PostsMigration::class,
            CommentsMigration::class,
            DateTimeFunctionMigration::class,
        ];
    }

    /** Migrations table name */
    const MIGRATIONS_TABLE = '_migrations';

    /** Migration column name */
    const MIGRATIONS_COLUMN_ID = 'id';

    /** Migration column name */
    const MIGRATIONS_COLUMN_CLASS = 'class';

    /** Seeds table name */
    const SEEDS_TABLE = SeedsRunner::SEEDS_TABLE;

    /**
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function migrate(ContainerInterface $container)
    {
        foreach ($this->getMigrations($container) as $class) {
            /** @var Migration $migration */
            $migration = new $class($container);
            $migration->migrate();
        }
    }

    /**
     * @param ContainerInterface $container
     *
     * @return void
     */
    public function rollback(ContainerInterface $container)
    {
        foreach ($this->getRollbacks($container) as $class) {
            /** @var Migration $migration */
            $migration = new $class($container);
            $migration->rollback();
        }

        $manager = $this->getConnection($container)->getSchemaManager();
        if ($manager->tablesExist(static::MIGRATIONS_TABLE) === true) {
            $manager->dropTable(static::MIGRATIONS_TABLE);
        }
        if ($manager->tablesExist(static::SEEDS_TABLE) === true) {
            $manager->dropTable(static::SEEDS_TABLE);
        }
    }

    /**
     * @param ContainerInterface $container
     *
     * @return Generator
     */
    private function getMigrations(ContainerInterface $container)
    {
        $connection = $this->getConnection($container);
        $manager    = $connection->getSchemaManager();

        if ($manager->tablesExist(static::MIGRATIONS_TABLE) === true) {
            $migrated = $this->readMigrated($connection);
        } else {
            $this->createMigrationsTable($manager);
            $migrated = [];
        }

        $notYetMigrated = array_diff($this->getMigrationsList(), $migrated);

        foreach ($notYetMigrated as $class) {
            yield $class;
            $this->saveMigration($connection, $class);
        }
    }

    /**
     * @param ContainerInterface $container
     *
     * @return Generator
     */
    private function getRollbacks(ContainerInterface $container)
    {
        $connection = $this->getConnection($container);
        $migrated   = $this->readMigrated($connection);

        foreach (array_reverse($migrated, true) as $index => $class) {
            yield $class;
            $this->removeMigration($connection, $index);
        }
    }

    /**
     * @param ContainerInterface $container
     *
     * @return Connection
     */
    private function getConnection(ContainerInterface $container)
    {
        return $container->get(Connection::class);
    }

    /**
     * @param AbstractSchemaManager $manager
     *
     * @return void
     */
    private function createMigrationsTable(AbstractSchemaManager $manager)
    {
        $table = new Table(static::MIGRATIONS_TABLE);

        $table
            ->addColumn(static::MIGRATIONS_COLUMN_ID, Type::INTEGER)
            ->setUnsigned(true)
            ->setAutoincrement(true);
        $table
            ->addColumn(static::MIGRATIONS_COLUMN_CLASS, Type::STRING)
            ->setLength(255);

        $table->setPrimaryKey([static::MIGRATIONS_COLUMN_ID]);
        $table->addUniqueIndex([static::MIGRATIONS_COLUMN_CLASS]);

        $manager->createTable($table);
    }

    /**
     * @param Connection $connection
     *
     * @return array
     */
    private function readMigrated($connection)
    {
        $builder  = $connection->createQueryBuilder();
        $migrated = [];

        if ($connection->getSchemaManager()->tablesExist(static::MIGRATIONS_TABLE) === true) {
            $migrations = $builder
                ->select(static::MIGRATIONS_COLUMN_ID, static::MIGRATIONS_COLUMN_CLASS)
                ->from(static::MIGRATIONS_TABLE)
                ->orderBy(static::MIGRATIONS_COLUMN_ID)
                ->execute()
                ->fetchAll();
            foreach ($migrations as $migration) {
                $index            = $migration[static::MIGRATIONS_COLUMN_ID];
                $class            = $migration[static::MIGRATIONS_COLUMN_CLASS];
                $migrated[$index] = $class;
            }
        }

        return $migrated;
    }

    /**
     * @param Connection $connection
     * @param string     $class
     *
     * @return void
     */
    private function saveMigration(Connection $connection, $class)
    {
        $connection->insert(static::MIGRATIONS_TABLE, [static::MIGRATIONS_COLUMN_CLASS => $class]);
    }

    /**
     * @param Connection $connection
     * @param int        $index
     *
     * @return void
     */
    private function removeMigration(Connection $connection, $index)
    {
        $connection->delete(static::MIGRATIONS_TABLE, [static::MIGRATIONS_COLUMN_ID => $index]);
    }
}
