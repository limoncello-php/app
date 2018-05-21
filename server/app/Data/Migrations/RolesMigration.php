<?php namespace App\Data\Migrations;

use App\Data\Models\Role as Model;
use Doctrine\DBAL\DBALException;
use Limoncello\Contracts\Data\MigrationInterface;
use Limoncello\Data\Migrations\MigrationTrait;

/**
 * @package App
 */
class RolesMigration implements MigrationInterface
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
            $this->primaryString(Model::FIELD_ID),
            $this->string(Model::FIELD_DESCRIPTION),
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
