<?php namespace App\Database\Migrations;

/**
 * @package App
 */
abstract class FunctionMigration extends Migration
{
    /** Function name */
    const FUNCTION_NAME = null;

    /** Function body */
    const FUNCTION_CREATE_STATEMENT = null;

    /**
     * @inheritdoc
     */
    public function migrate()
    {
        $this->dropFunctionIfExists();

        $statement = static::FUNCTION_CREATE_STATEMENT;
        assert('$statement !== null', 'Function create statement should be specified.');

        $this->getConnection()->exec($statement);
    }

    /**
     * @inheritdoc
     */
    public function rollback()
    {
        $this->dropFunctionIfExists();
    }

    /**
     * @return void
     */
    private function dropFunctionIfExists()
    {
        $name = static::FUNCTION_NAME;
        assert('$name !== null', 'Function name should be specified.');

        $this->getConnection()->exec("DROP FUNCTION IF EXISTS $name");
    }
}
