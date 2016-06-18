<?php namespace App\Database\Migrations;

use App\Database\Models\Post;
use PDO;

/**
 * @package App
 */
class PostsMigration extends Migration
{
    /** @inheritdoc */
    const MODEL_CLASS = Post::class;

    /**
     * @inheritdoc
     */
    public function migrate(PDO $pdo)
    {
        $this->createTable($pdo, Post::TABLE_NAME, [
            $this->int(Post::FIELD_ID, true),
            $this->int(Post::FIELD_ID_BOARD),
            $this->int(Post::FIELD_ID_USER),
            $this->string(Post::FIELD_TITLE),
            $this->text(Post::FIELD_TEXT),
            $this->timestamp(Post::FIELD_CREATED_AT),
            $this->timestamp(Post::FIELD_UPDATED_AT),
            $this->timestamp(Post::FIELD_DELETED_AT),

            $this->primary(Post::FIELD_ID),

            $this->relationship(Post::REL_BOARD),
            $this->relationship(Post::REL_USER),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rollback(PDO $pdo)
    {
        $this->dropTable($pdo, Post::TABLE_NAME);
    }
}
