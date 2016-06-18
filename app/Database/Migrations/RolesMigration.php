<?php namespace App\Database\Migrations;

use App\Database\Models\Role;
use PDO;

/**
 * @package App
 */
class RolesMigration extends Migration
{
    /** @inheritdoc */
    const MODEL_CLASS = Role::class;

    /**
     * @inheritdoc
     */
    public function migrate(PDO $pdo)
    {
        $this->createTable($pdo, Role::TABLE_NAME, [
            $this->int(Role::FIELD_ID, true),
            $this->string(Role::FIELD_NAME),

            $this->timestamp(Role::FIELD_CREATED_AT),
            $this->timestamp(Role::FIELD_UPDATED_AT),
            $this->timestamp(Role::FIELD_DELETED_AT),

            $this->primary(Role::FIELD_ID),
            $this->unique(Role::FIELD_NAME),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rollback(PDO $pdo)
    {
        $this->dropTable($pdo, Role::TABLE_NAME);
    }
}
