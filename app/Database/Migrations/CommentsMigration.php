<?php namespace App\Database\Migrations;

use App\Database\Models\Comment;
use PDO;

/**
 * @package App
 */
class CommentsMigration extends Migration
{
    /** @inheritdoc */
    const MODEL_CLASS = Comment::class;

    /**
     * @inheritdoc
     */
    public function migrate(PDO $pdo)
    {
        $this->createTable($pdo, Comment::TABLE_NAME, [
            $this->int(Comment::FIELD_ID, true),
            $this->int(Comment::FIELD_ID_POST),
            $this->int(Comment::FIELD_ID_USER),
            $this->text(Comment::FIELD_TEXT),
            $this->timestamp(Comment::FIELD_CREATED_AT),
            $this->timestamp(Comment::FIELD_UPDATED_AT),
            $this->timestamp(Comment::FIELD_DELETED_AT),

            $this->primary(Comment::FIELD_ID),

            $this->relationship(Comment::REL_POST),
            $this->relationship(Comment::REL_USER),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rollback(PDO $pdo)
    {
        $this->dropTable($pdo, Comment::TABLE_NAME);
    }
}
