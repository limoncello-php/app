<?php namespace App\Database\Migrations;

use App\Database\Models\Comment as Model;

/**
 * @package App
 */
class CommentsMigration extends TableMigration
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
            $this->relationship(Model::REL_POST),
            $this->text(Model::FIELD_TEXT),
            $this->timestamps(),
        ]);
    }
}
