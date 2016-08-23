<?php namespace App\Database\Seeds;

use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Faker\Generator;
use Interop\Container\ContainerInterface;

/**
 * @package App
 */
abstract class Seeder
{
    /**
     * @return void
     */
    abstract public function run();

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

    /**
     * @return Generator
     */
    protected function getFaker()
    {
        return $this->getContainer()->get(Generator::class);
    }

    /**
     * @param string                          $tableName
     * @param null|CompositeExpression|string $where
     * @param null|int                        $limit
     *
     * @return array
     */
    protected function readTable($tableName, $where = null, $limit = null)
    {
        $builder = $this->getConnection()->createQueryBuilder();

        $builder
            ->select($tableName . '.*')
            ->from($tableName, $tableName);

        $where === null ?: $builder->where($where);
        $limit === null ?: $builder->setMaxResults($limit);

        $result = $builder->execute()->fetchAll();

        return $result;
    }

    /**
     * @param string                     $tableName
     * @param CompositeExpression|string $where
     *
     * @return array|false
     */
    protected function readRow($tableName, $where)
    {
        $builder = $this->getConnection()->createQueryBuilder();

        $builder
            ->select($tableName . '.*')
            ->from($tableName, $tableName)
            ->where($where)
            ->setMaxResults(1);

        $result = $builder->execute()->fetch();

        return $result;
    }

    /**
     * @param int     $records
     * @param string  $tableName
     * @param Closure $fieldsClosure
     */
    protected function seedTable($records, $tableName, Closure $fieldsClosure)
    {
        $connection = $this->getConnection();
        for ($i = 0; $i !== (int)$records; $i++) {
            $fields = $fieldsClosure();
            $this->insertRow($tableName, $connection, $fields);
        }
    }

    /**
     * @param string $tableName
     * @param array  $fields
     *
     * @return int|null
     */
    protected function seedRow($tableName, array $fields)
    {
        $connection = $this->getConnection();
        $index      = $this->insertRow($tableName, $connection, $fields);

        return $index;
    }

    /**
     * @return string
     */
    protected function getCurrentDateTime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    protected function getLastInsertId()
    {
        return $this->getConnection()->lastInsertId();
    }

    /**
     * @param string     $tableName
     * @param Connection $connection
     * @param array      $fields
     *
     * @return int|null
     */
    private function insertRow($tableName, Connection $connection, array $fields)
    {
        $quotedFields = [];
        foreach ($fields as $column => $value) {
            $quotedFields["`$column`"] = $value;
        }

        $index = null;
        try {
            $result = $connection->insert($tableName, $quotedFields);
            $index  = $connection->lastInsertId();
        } catch (UniqueConstraintViolationException $e) {
            // ignore non-unique records
            $result = true;
        }
        if ($result === false) {
            assert('$result !== false', 'Statement execution failed');
        }

        return $index;
    }
}
