<?php namespace App\Database\Migrations;

use App\Database\Models\Board;
use PDO;

/**
 * @package App
 */
class BoardsMigration extends Migration
{
    /** @inheritdoc */
    const MODEL_CLASS = Board::class;

    /**
     * @inheritdoc
     */
    public function migrate(PDO $pdo)
    {
        $this->createTable($pdo, Board::TABLE_NAME, [
            $this->int(Board::FIELD_ID, true),
            $this->string(Board::FIELD_TITLE),

            $this->timestamp(Board::FIELD_CREATED_AT),
            $this->timestamp(Board::FIELD_UPDATED_AT),
            $this->timestamp(Board::FIELD_DELETED_AT),

            $this->primary(Board::FIELD_ID),
            $this->unique(Board::FIELD_TITLE),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rollback(PDO $pdo)
    {
        $this->dropTable($pdo, Board::TABLE_NAME);
    }
}
