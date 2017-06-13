<?php namespace App\Data\Migrations;

use App\Data\Models\Board as Model;
use Limoncello\Contracts\Data\MigrationInterface;
use Limoncello\Data\Migrations\MigrationTrait;

/**
 * @package App
 */
class BoardsMigration implements MigrationInterface
{
    use MigrationTrait;

    /**
     * @inheritdoc
     */
    public function migrate()
    {
        $this->createTable(Model::class, [
            $this->primaryInt(Model::FIELD_ID),
            $this->string(Model::FIELD_TITLE),
            $this->timestamps(),

            $this->unique([Model::FIELD_TITLE]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rollback()
    {
        $this->dropTableIfExists(Model::class);
    }
}
