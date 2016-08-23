<?php namespace App\Database\Migrations;

use App\Database\Models\User as Model;

/**
 * @package App
 */
class UsersMigration extends Migration
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
            $this->relationship(Model::REL_ROLE),
            $this->string(Model::FIELD_TITLE),
            $this->string(Model::FIELD_FIRST_NAME),
            $this->string(Model::FIELD_LAST_NAME),
            $this->string(Model::FIELD_LANGUAGE),
            $this->string(Model::FIELD_EMAIL),
            $this->string(Model::FIELD_PASSWORD_HASH),
            $this->nullableString(Model::FIELD_API_TOKEN),
            $this->timestamps(),

            $this->unique([Model::FIELD_EMAIL]),
        ]);
    }
}
