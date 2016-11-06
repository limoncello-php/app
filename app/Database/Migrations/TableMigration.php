<?php namespace App\Database\Migrations;

use Closure;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Limoncello\JsonApi\Contracts\Models\ModelSchemesInterface;
use Limoncello\JsonApi\Models\RelationshipTypes;

/**
 * @package App
 */
abstract class TableMigration extends Migration
{
    /** Model class */
    const MODEL_CLASS = null;

    /**
     * @inheritdoc
     */
    public function rollback()
    {
        $tableName = $this->getTableName();
        if ($this->getSchemaManager()->tablesExist($tableName) === true) {
            $this->getSchemaManager()->dropTable($tableName);
        }
    }

    /**
     * @return AbstractSchemaManager
     */
    protected function getSchemaManager()
    {
        return $this->getConnection()->getSchemaManager();
    }

    /**
     * @return ModelSchemesInterface
     */
    protected function getModelSchemes()
    {
        return $this->getContainer()->get(ModelSchemesInterface::class);
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        $modelClass = $this->getModelClass();

        return $this->getTableNameForClass($modelClass);
    }

    /**
     * @return string
     */
    protected function getPrimaryKeyName()
    {
        $modelClass = $this->getModelClass();

        return $this->getPrimaryKeyNameForClass($modelClass);
    }

    /**
     * @param Closure[]   $expressions
     * @param null|string $name
     *
     * @return Table
     */
    protected function createTable(array $expressions = [], $name = null)
    {
        $name  = $name === null ? $this->getTableName() : $name;
        $table = new Table($name);

        foreach ($expressions as $expression) {
            /** @var Closure $expression */
            $expression($table);
        }

        $this->getSchemaManager()->dropAndCreateTable($table);

        return $table;
    }

    /**
     * @param string $name
     *
     * @return Closure
     */
    protected function primaryInt($name)
    {
        return function (Table $table) use ($name) {
            $table->addColumn($name, Type::INTEGER)->setAutoincrement(true)->setUnsigned(true)->setNotnull(true);
            $table->setPrimaryKey([$name]);
        };
    }

    /**
     * @param string $name
     *
     * @return Closure
     */
    protected function string($name)
    {
        return function (Table $table) use ($name) {
            $modelClass = $this->getModelClass();
            $length = $this->getModelSchemes()->getAttributeLength($modelClass, $name);
            $table->addColumn($name, Type::STRING, ['length' => $length])->setNotnull(true);
        };
    }

    /**
     * @param string $name
     *
     * @return Closure
     */
    protected function nullableString($name)
    {
        return function (Table $table) use ($name) {
            $modelClass = $this->getModelClass();
            $length = $this->getModelSchemes()->getAttributeLength($modelClass, $name);
            $table->addColumn($name, Type::STRING, ['length' => $length])->setNotnull(false);
        };
    }

    /**
     * @param string $name
     *
     * @return Closure
     */
    protected function text($name)
    {
        return function (Table $table) use ($name) {
            $table->addColumn($name, Type::TEXT)->setNotnull(true);
        };
    }

    /**
     * @param string    $name
     * @param null|bool $default
     *
     * @return Closure
     */
    protected function bool($name, $default = null)
    {
        return function (Table $table) use ($name, $default) {
            $column = $table->addColumn($name, Type::BOOLEAN)->setNotnull(true);
            if ($default !== null && is_bool($default) === true) {
                $column->setDefault($default);
            }
        };
    }

    /**
     * @return Closure
     */
    protected function timestamps()
    {
        $createdAt = \App\Database\Models\Model::FIELD_CREATED_AT;
        $updatedAt = \App\Database\Models\Model::FIELD_UPDATED_AT;
        $deletedAt = \App\Database\Models\Model::FIELD_DELETED_AT;

        $modelClass = $this->getModelClass();

        // a list of data columns and `nullable` flag
        $datesToAdd = [];
        if ($this->getModelSchemes()->hasAttributeType($modelClass, $createdAt) === true) {
            $datesToAdd[$createdAt] = true;
        }
        if ($this->getModelSchemes()->hasAttributeType($modelClass, $updatedAt) === true) {
            $datesToAdd[$updatedAt] = false;
        }
        if ($this->getModelSchemes()->hasAttributeType($modelClass, $deletedAt) === true) {
            $datesToAdd[$deletedAt] = false;
        }

        return function (Table $table) use ($datesToAdd) {
            foreach ($datesToAdd as $column => $isNullable) {
                $table->addColumn($column, Type::DATETIME)->setNotnull($isNullable);
            }
        };
    }

    /**
     * @param string $name
     *
     * @return Closure
     */
    protected function datetime($name)
    {
        return function (Table $table) use ($name) {
            $table->addColumn($name, Type::DATETIME)->setNotnull(true);
        };
    }

    /**
     * @param string $name
     *
     * @return Closure
     */
    protected function nullableDatetime($name)
    {
        return function (Table $table) use ($name) {
            $table->addColumn($name, Type::DATETIME)->setNotnull(false);
        };
    }

    /**
     * @param string[]    $names
     * @param null|string $indexName
     *
     * @return Closure
     */
    protected function unique(array $names, $indexName = null)
    {
        if ($indexName === null) {
            $indexName = $this->createUniqueIndexName($names);
        }

        return function (Table $table) use ($names, $indexName) {
            $table->addUniqueIndex($names, $indexName);
        };
    }

    /**
     * @param string $name
     * @param string $referredClass
     *
     * @return Closure
     */
    protected function foreignRelationship($name, $referredClass)
    {
        $tableName = $this->getTableNameForClass($referredClass);
        $pkName    = $this->getModelSchemes()->getPrimaryKey($referredClass);

        return $this->foreignColumn($name, $tableName, $pkName);
    }

    /**
     * @param string $localKey
     * @param string $foreignTable
     * @param $foreignKey $foreignTable
     *
     * @return Closure
     */
    protected function foreignColumn($localKey, $foreignTable, $foreignKey)
    {
        return function (Table $table) use ($localKey, $foreignTable, $foreignKey) {
            $table->addColumn($localKey, Type::INTEGER)->setUnsigned(true)->setNotnull(true);
            $table->addForeignKeyConstraint($foreignTable, [$localKey], [$foreignKey]);
        };
    }

    /**
     * @param string $name
     *
     * @return Closure
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function relationship($name)
    {
        $modelClass = $this->getModelClass();

        $hasRelationship = $this->getModelSchemes()->hasRelationship($modelClass, $name);
        if ($hasRelationship === false) {
            assert('$hasRelationship === true', "Relationship `$name` not found for model `$modelClass`.");
        }

        $type = $this->getModelSchemes()->getRelationshipType($modelClass, $name);
        $canBeCreated = $type === RelationshipTypes::BELONGS_TO || $type === RelationshipTypes::BELONGS_TO_MANY;
        if ($canBeCreated === false) {
            assert(
                '$canBeCreated === true',
                "Relationship `$name` for model `$modelClass` must be either `belongsTo` or `belongsToMany`."
            );
        }

        $localKey = $this->getModelSchemes()->getForeignKey($modelClass, $name);
        if ($type === RelationshipTypes::BELONGS_TO) {
            $otherModelClass = $this->getModelSchemes()->getReverseModelClass($modelClass, $name);
            $foreignTable    = $this->getModelSchemes()->getTable($otherModelClass);
            $foreignKey      = $this->getModelSchemes()->getPrimaryKey($otherModelClass);
        } else {
            // if we are here this is belongsToMany relationship
            list ($foreignTable, $foreignKey) =
                $this->getModelSchemes()->getBelongsToManyRelationship($modelClass, $name);
        }

        return $this->foreignColumn($localKey, $foreignTable, $foreignKey);
    }

    /**
     * @param string $modelClass
     *
     * @return string
     */
    protected function getTableNameForClass($modelClass)
    {
        $hasClass = $this->getModelSchemes()->hasClass($modelClass);
        if ($hasClass === false) {
            assert('$hasClass !== null', "Table name is not specified for model '$modelClass'.");
        }

        $tableName = $this->getModelSchemes()->getTable($modelClass);

        return $tableName;
    }

    /**
     * @param string $modelClass
     *
     * @return string
     */
    protected function getPrimaryKeyNameForClass($modelClass)
    {
        $hasClass = $this->getModelSchemes()->hasClass($modelClass);
        if ($hasClass === false) {
            assert('$hasClass !== null', "Table name is not specified for model '$modelClass'.");
        }

        $primary = $this->getModelSchemes()->getPrimaryKey($modelClass);

        return $primary;
    }

    /**
     * @return string
     */
    private function getModelClass()
    {
        $modelClass = static::MODEL_CLASS;
        assert('$modelClass !== null', 'Model class should be set in migration');

        return $modelClass;
    }

    /**
     * @param string[] $names
     *
     * @return string
     */
    protected function createUniqueIndexName(array $names)
    {
        $indexName = null;

        if (empty($names) === false) {
            $indexName = 'UN' . 'IQ';
            foreach ($names as $name) {
                $indexName .= ('_' . strtoupper($name));
            }
        }

        return $indexName;
    }
}
