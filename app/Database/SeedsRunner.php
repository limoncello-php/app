<?php namespace App\Database;

use App\Database\Seeds\Seeder;
use App\Database\Seeds\Testing\BoardsSeeder;
use App\Database\Seeds\Testing\CommentsSeeder;
use App\Database\Seeds\Testing\PostsSeeder;
use App\Database\Seeds\Testing\RolesSeeder;
use App\Database\Seeds\Testing\UsersSeeder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Faker\Factory;
use Faker\Generator;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SeedsRunner
{
    /**
     * @return string[]
     */
    protected function getSeedsList()
    {
        return [
            RolesSeeder::class,
            UsersSeeder::class,
            BoardsSeeder::class,
            PostsSeeder::class,
            CommentsSeeder::class,
        ];
    }

    /**
     * @param Container $container
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function run(Container $container)
    {
        $faker                       = Factory::create();
        $container[Generator::class] = $faker;

        foreach ($this->getSeeds($container) as $seederClass) {
            $faker->seed(crc32($seederClass));
            /** @var Seeder $seeder */
            $seeder = new $seederClass($container);
            $seeder->run();
        }
    }

    /** Seeds table name */
    const SEEDS_TABLE = '_seeds';

    /** Seed column name */
    const SEEDS_COLUMN_ID = 'id';

    /** Seed column name */
    const SEEDS_COLUMN_CLASS = 'class';

    /**
     * @param ContainerInterface $container
     *
     * @return Generator
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function getSeeds(ContainerInterface $container)
    {
        $connection = $this->getConnection($container);
        $manager    = $connection->getSchemaManager();

        if ($manager->tablesExist(static::SEEDS_TABLE) === true) {
            $seeded = $this->readSeeded($connection);
        } else {
            $this->createSeedsTable($manager);
            $seeded = [];
        }

        $notYetSeeded = array_diff($this->getSeedsList(), $seeded);

        foreach ($notYetSeeded as $class) {
            yield $class;
            $this->saveSeed($connection, $class);
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
    private function createSeedsTable(AbstractSchemaManager $manager)
    {
        $table = new Table(static::SEEDS_TABLE);

        $table
            ->addColumn(static::SEEDS_COLUMN_ID, Type::INTEGER)
            ->setUnsigned(true)
            ->setAutoincrement(true);
        $table
            ->addColumn(static::SEEDS_COLUMN_CLASS, Type::STRING)
            ->setLength(255);

        $table->setPrimaryKey([static::SEEDS_COLUMN_ID]);
        $table->addUniqueIndex([static::SEEDS_COLUMN_CLASS]);

        $manager->createTable($table);
    }

    /**
     * @param Connection $connection
     *
     * @return array
     */
    private function readSeeded($connection)
    {
        $builder = $connection->createQueryBuilder();
        $seeded  = [];

        if ($connection->getSchemaManager()->tablesExist(static::SEEDS_TABLE) === true) {
            $seeds = $builder
                ->select(static::SEEDS_COLUMN_ID, static::SEEDS_COLUMN_CLASS)
                ->from(static::SEEDS_TABLE)
                ->orderBy(static::SEEDS_COLUMN_ID)
                ->execute()
                ->fetchAll();
            foreach ($seeds as $seed) {
                $index          = $seed[static::SEEDS_COLUMN_ID];
                $class          = $seed[static::SEEDS_COLUMN_CLASS];
                $seeded[$index] = $class;
            }
        }

        return $seeded;
    }

    /**
     * @param Connection $connection
     * @param string     $class
     *
     * @return void
     */
    private function saveSeed(Connection $connection, $class)
    {
        $connection->insert(static::SEEDS_TABLE, [static::SEEDS_COLUMN_CLASS => $class]);
    }
}
