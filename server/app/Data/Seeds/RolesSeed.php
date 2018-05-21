<?php namespace App\Data\Seeds;

use App\Data\Models\Role as Model;
use Doctrine\DBAL\DBALException;
use Exception;
use Limoncello\Contracts\Data\SeedInterface;
use Limoncello\Data\Seeds\SeedTrait;

/**
 * @package App
 */
class RolesSeed implements SeedInterface
{
    use SeedTrait;

    /** Role name */
    const ROLE_ADMIN = 'admin';

    /** Role name */
    const ROLE_MODERATOR = 'moderator';

    /** Role name */
    const ROLE_USER = 'user';

    /**
     * @inheritdoc
     *
     * @throws DBALException
     * @throws Exception
     */
    public function run(): void
    {
        $now = $this->now();
        $this->seedRowData(Model::TABLE_NAME, [
            Model::FIELD_ID          => self::ROLE_ADMIN,
            Model::FIELD_DESCRIPTION => 'Administrator',
            Model::FIELD_CREATED_AT  => $now,
        ]);
        $this->seedRowData(Model::TABLE_NAME, [
            Model::FIELD_ID          => self::ROLE_MODERATOR,
            Model::FIELD_DESCRIPTION => 'Moderator',
            Model::FIELD_CREATED_AT  => $now,
        ]);
        $this->seedRowData(Model::TABLE_NAME, [
            Model::FIELD_ID          => self::ROLE_USER,
            Model::FIELD_DESCRIPTION => 'User',
            Model::FIELD_CREATED_AT  => $now,
        ]);
    }
}
