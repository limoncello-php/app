<?php namespace App\Database\Migrations;

use App\Database\Models\Board as Model;

/**
 * @package App
 */
class BoardsMigration extends Migration
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
            $this->string(Model::FIELD_TITLE),
            $this->datetime(Model::FIELD_CREATED_AT),
            $this->nullableDatetime(Model::FIELD_UPDATED_AT),
            $this->nullableDatetime(Model::FIELD_DELETED_AT),

            $this->unique([Model::FIELD_TITLE]),
        ]);
    }
}
