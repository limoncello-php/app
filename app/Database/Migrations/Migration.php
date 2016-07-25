<?php namespace App\Database\Migrations;

use App\Database\Models\Model;
use App\Database\Models\ModelInterface;
use Closure;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Limoncello\Models\RelationshipTypes;

/**
 * @package App
 */
abstract class Migration
{
    /** Model class */
    const MODEL_CLASS = null;

    /**
     * @return void
     */
    abstract public function migrate();

    /**
     * @var AbstractSchemaManager
     */
    private $schemaManager;

    /**
     * @param AbstractSchemaManager $schemaManager
     */
    public function __construct(AbstractSchemaManager $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

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
        return $this->schemaManager;
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
     * @param string    $name
     * @param Closure[] $expressions
     *
     * @return Table
     */
    protected function createTable($name, array $expressions = [])
    {
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
            /** @var ModelInterface $modelClass*/
            $lengths   = $modelClass::getAttributeLengths();
            $hasLength = array_key_exists($name, $lengths);
            assert('$hasLength === true', "String length is not specified for column '$name' in model '$modelClass'.");
            $hasLength ?: null;
            $length = $lengths[$name];
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
            /** @var ModelInterface $modelClass*/
            $lengths   = $modelClass::getAttributeLengths();
            $hasLength = array_key_exists($name, $lengths);
            assert('$hasLength === true', "String length is not specified for column '$name' in model '$modelClass'.");
            $hasLength ?: null;
            $length = $lengths[$name];
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
     * @param string $name
     *
     * @return Closure
     */
    protected function bool($name)
    {
        return function (Table $table) use ($name) {
            $table->addColumn($name, Type::BOOLEAN)->setNotnull(true);
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
     * @param string[] $names
     *
     * @return Closure
     */
    protected function unique(array $names)
    {
        return function (Table $table) use ($names) {
            $table->addUniqueIndex($names);
        };
    }

    /**
     * @param string $name
     * @param string $referredClass
     *
     * @return Closure
     */
    protected function foreignInt($name, $referredClass)
    {
        return function (Table $table) use ($name, $referredClass) {
            $table->addColumn($name, Type::INTEGER)->setUnsigned(true)->setNotnull(true);
            $tableName = $this->getTableNameForClass($referredClass);
            /** @var Model $referredClass*/
            assert('$tableName !== null', "Table name is not specified for model '$referredClass'.");
            $pkName = $referredClass::FIELD_ID;
            $table->addForeignKeyConstraint($tableName, [$name], [$pkName]);
        };
    }

    /**
     * @param string $name
     *
     * @return Closure
     */
    protected function relationship($name)
    {
        /** @var ModelInterface $modelClass */
        $modelClass    = $this->getModelClass();
        $relationships = $modelClass::getRelationships();
        $relFound      = isset($relationships[RelationshipTypes::BELONGS_TO][$name]);
        $relFound ?: null;
        assert('$relFound === true', "Belongs-to relationship '$name' not found.");
        list ($referencedClass, $foreignKey) = $relationships[RelationshipTypes::BELONGS_TO][$name];
        return $this->foreignInt($foreignKey, $referencedClass);
    }

    /**
     * @param string $modelClass
     *
     * @return string
     */
    protected function getTableNameForClass($modelClass)
    {
        /** @var Model $modelClass*/
        $tableName = $modelClass::TABLE_NAME;
        assert('$tableName !== null', "Table name is not specified for model '$modelClass'.");

        return $tableName;
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
}
