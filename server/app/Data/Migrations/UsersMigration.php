<?php namespace App\Data\Migrations;

use App\Data\Models\User as Model;
use Doctrine\DBAL\DBALException;
use Limoncello\Contracts\Data\MigrationInterface;
use Limoncello\Data\Migrations\MigrationTrait;

/**
 * @package App
 */
class UsersMigration implements MigrationInterface
{
    use MigrationTrait;

    /**
     * @inheritdoc
     *
     * @throws DBALException
     */
    public function migrate(): void
    {
        $this->createTable(Model::class, [
            $this->primaryInt(Model::FIELD_ID),
            $this->relationship(Model::REL_ROLE, true),
            $this->string(Model::FIELD_FIRST_NAME),
            $this->string(Model::FIELD_LAST_NAME),
            $this->string(Model::FIELD_EMAIL),
            $this->string(Model::FIELD_PASSWORD_HASH),
            $this->timestamps(),

            $this->unique([Model::FIELD_EMAIL]),
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
