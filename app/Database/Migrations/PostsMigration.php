<?php namespace App\Database\Migrations;

use App\Database\Models\Post as Model;

/**
 * @package App
 */
class PostsMigration extends Migration
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
            $this->relationship(Model::REL_USER),
            $this->relationship(Model::REL_BOARD),
            $this->string(Model::FIELD_TITLE),
            $this->text(Model::FIELD_TEXT),
            $this->datetime(Model::FIELD_CREATED_AT),
            $this->nullableDatetime(Model::FIELD_UPDATED_AT),
            $this->nullableDatetime(Model::FIELD_DELETED_AT),
        ]);
    }
}
