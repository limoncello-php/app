<?php namespace App\Database\Migrations;

use App\Database\Models\User;
use PDO;

/**
 * @package App
 */
class UsersMigration extends Migration
{
    /** @inheritdoc */
    const MODEL_CLASS = User::class;

    /**
     * @inheritdoc
     */
    public function migrate(PDO $pdo)
    {
        $this->createTable($pdo, User::TABLE_NAME, [
            $this->int(User::FIELD_ID, true),
            $this->int(User::FIELD_ID_ROLE),
            $this->string(User::FIELD_TITLE),
            $this->string(User::FIELD_FIRST_NAME),
            $this->string(User::FIELD_LAST_NAME),
            $this->string(User::FIELD_LANGUAGE),
            $this->string(User::FIELD_EMAIL),
            $this->string(User::FIELD_PASSWORD_HASH),
            $this->string(User::FIELD_API_TOKEN),
            $this->timestamp(User::FIELD_CREATED_AT),
            $this->timestamp(User::FIELD_UPDATED_AT),
            $this->timestamp(User::FIELD_DELETED_AT),

            $this->primary(User::FIELD_ID),
            $this->unique(User::FIELD_EMAIL),

            $this->relationship(User::REL_ROLE),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rollback(PDO $pdo)
    {
        $this->dropTable($pdo, User::TABLE_NAME);
    }
}
