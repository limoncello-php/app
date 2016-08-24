<?php namespace App\Database\Migrations;

use App\Database\Models\Role as Model;

/**
 * @package App
 */
class RolesMigration extends TableMigration
{
    /** @inheritdoc */
    const MODEL_CLASS = Model::class;

    /**
     * @inheritdoc
     */
    public function migrate()
    {
        $this->createTable([
            $this->primaryInt(Model::FIELD_ID),
            $this->string(Model::FIELD_NAME),
            $this->timestamps(),

            $this->unique([Model::FIELD_NAME]),
        ]);
    }
}
