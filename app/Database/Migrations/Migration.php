<?php namespace App\Database\Migrations;

use App\Database\Models\Model;
use App\Database\Models\ModelInterface;
use Limoncello\Models\RelationshipTypes;
use PDO;

/**
 * @package App
 */
abstract class Migration
{
    /** Model class */
    const MODEL_CLASS = null;

    /**
     * @param PDO $pdo
     *
     * @return void
     */
    abstract public function migrate(PDO $pdo);

    /**
     * @param PDO $pdo
     *
     * @return void
     */
    abstract public function rollback(PDO $pdo);

    /**
     * Constructor.
     */
    public function __construct()
    {
        $migrationClass = static::class;
        assert(static::MODEL_CLASS !== null, "Model class is not set for migration '$migrationClass'.");
    }

    /**
     * @param PDO    $pdo
     * @param string $tableName
     * @param array  $fields
     *
     * @return $this
     */
    protected function createTable(PDO $pdo, $tableName, array $fields)
    {
        $statement = $this->getCreateTableStatement($tableName, $fields);
        $result    = $pdo->exec($statement);
        assert($result !== false, 'Statement execution failed');

        return $this;
    }

    /**
     * @param PDO    $pdo
     * @param string $tableName
     *
     * @return $this
     */
    protected function dropTable(PDO $pdo, $tableName)
    {
        $statement = "DROP TABLE IF EXISTS $tableName";
        $result    = $pdo->exec($statement);
        assert($result !== false, 'Statement execution failed');

        return $this;
    }

    /**
     * @param string $tableName
     * @param array  $fields
     *
     * @return string
     */
    protected function getCreateTableStatement($tableName, array $fields)
    {
        $columns   = implode(", ", $fields);
        $statement = "CREATE TABLE IF NOT EXISTS $tableName ($columns) DEFAULT CHARACTER SET = utf8 ENGINE=INNODB";

        return $statement;
    }

    /**
     * @param string $column
     * @param string $foreignTable
     * @param string $foreignColumn
     * @param string $onDelete
     *
     * @return string
     */
    protected function foreignKey($column, $foreignTable, $foreignColumn, $onDelete = 'CASCADE')
    {
        return "FOREIGN KEY($column) REFERENCES $foreignTable($foreignColumn) ON DELETE $onDelete";
    }

    /**
     * @param string $name
     * @param string $onDelete
     *
     * @return string
     */
    protected function relationship($name, $onDelete = 'CASCADE')
    {
        /** @var ModelInterface $modelClass */
        $modelClass = static::MODEL_CLASS;

        list($otherModelClass, $column) = $modelClass::getRelationships()[RelationshipTypes::BELONGS_TO][$name];

        /** @var Model $otherModelClass */

        $foreignTable  = $otherModelClass::TABLE_NAME;
        $foreignColumn = $otherModelClass::FIELD_ID;

        return $this->foreignKey($column, $foreignTable, $foreignColumn, $onDelete);
    }

    /**
     * @param string $column
     * @param bool   $autoIncrement
     *
     * @return string
     */
    protected function int($column, $autoIncrement = false)
    {
        return $autoIncrement === true ? "$column INT NOT NULL AUTO_INCREMENT" : "$column INT NOT NULL";
    }

    /**
     * @param string $column
     *
     * @return string
     */
    protected function timestamp($column)
    {
        return $column . " TIMESTAMP NULL DEFAULT NULL";
    }

    /**
     * @param string $column
     *
     * @return string
     */
    protected function string($column)
    {
        /** @var ModelInterface $modelClass */
        $modelClass = static::MODEL_CLASS;
        $length     = $modelClass::getAttributeLengths()[$column];
        return "$column VARCHAR($length) NOT NULL";
    }

    /**
     * @param string $column
     *
     * @return string
     */
    protected function text($column)
    {
        return "$column TEXT NOT NULL";
    }

    /**
     * @param string[]|string $columns
     *
     * @return string
     */
    protected function primary($columns)
    {
        $columns = is_array($columns) === true ? implode(',', $columns) : $columns;
        return "PRIMARY KEY($columns)";
    }

    /**
     * @param string[]|string $columns
     *
     * @return string
     */
    protected function unique($columns)
    {
        $columns = is_array($columns) === true ? implode(',', $columns) : $columns;
        return "UNIQUE($columns)";
    }
}
