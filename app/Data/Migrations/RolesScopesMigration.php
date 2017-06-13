<?php namespace App\Data\Migrations;

use App\Data\Models\Role;
use App\Data\Models\RoleScope as Model;
use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Data\MigrationInterface;
use Limoncello\Data\Migrations\MigrationTrait;
use Limoncello\Passport\Entities\DatabaseScheme;
use Limoncello\Passport\Entities\Scope;

/**
 * @package App
 */
class RolesScopesMigration implements MigrationInterface
{
    use MigrationTrait;

    /**
     * @inheritdoc
     */
    public function migrate()
    {
        $this->createTable(Model::class, [
            $this->primaryInt(Model::FIELD_ID),
            $this->foreignRelationship(Model::FIELD_ID_ROLE, Role::class),
            $this->foreignColumn(
                Model::FIELD_ID_SCOPE,
                DatabaseScheme::TABLE_SCOPES,
                Scope::FIELD_ID,
                Type::STRING,
                true
            ),
            $this->timestamps(),
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
