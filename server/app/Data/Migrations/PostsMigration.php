<?php namespace App\Data\Migrations;

use App\Data\Models\Post as Model;
use Limoncello\Contracts\Data\MigrationInterface;
use Limoncello\Data\Migrations\MigrationTrait;

/**
 * @package App
 */
class PostsMigration implements MigrationInterface
{
    use MigrationTrait;

    /**
     * @inheritdoc
     */
    public function migrate(): void
    {
        $this->createTable(Model::class, [
            $this->primaryInt(Model::FIELD_ID),
            $this->relationship(Model::REL_USER, true),
            $this->relationship(Model::REL_BOARD, true),
            $this->string(Model::FIELD_TITLE),
            $this->text(Model::FIELD_TEXT),
            $this->timestamps(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rollback(): void
    {
        $this->dropTableIfExists(Model::class);
    }
}
