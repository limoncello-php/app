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
            $this->timestamps(),

            $this->unique([Model::FIELD_TITLE]),
        ]);
    }
}
