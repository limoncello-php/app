<?php namespace App\Database\Seeds\Testing;

use App\Database\Models\Role as Model;
use App\Database\Seeds\Seeder;

/**
 * @package App
 */
class RolesSeeder extends Seeder
{
    /**
     * Pre-defined role name.
     */
    const NAME_ADMINS = 'Admins';

    /**
     * Pre-defined role name.
     */
    const NAME_USERS = 'Users';

    /**
     * @return void
     */
    public function run()
    {
        $curDateTime = $this->getCurrentDateTime();

        // predefined roles
        $this->seedRow(Model::TABLE_NAME, [
            Model::FIELD_NAME       => self::NAME_ADMINS,
            Model::FIELD_CREATED_AT => $curDateTime,
        ]);
        $this->seedRow(Model::TABLE_NAME, [
            Model::FIELD_NAME       => self::NAME_USERS,
            Model::FIELD_CREATED_AT => $curDateTime,
        ]);
    }
}
