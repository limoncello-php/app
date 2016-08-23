<?php namespace App\Database\Seeds\Testing;

use App\Database\Models\Role;
use App\Database\Models\User as Model;
use App\Database\Seeds\Seeder;
use Limoncello\Crypt\Contracts\HasherInterface;

/**
 * @package App
 */
class UsersSeeder extends Seeder
{
    /** Seeding constant */
    const ADMIN_EMAIL = 'admin@admins.tld';

    /** Seeding constant */
    const USER_EMAIL = 'user@users.tld';

    /** Seeding constant */
    const TEST_PASSWORD = 'password';

    /**
     * @return void
     */
    public function run()
    {
        /** @var HasherInterface $hasher */
        $hasher      = $this->getContainer()->get(HasherInterface::class);
        $curDateTime = $this->getCurrentDateTime();
        $faker       = $this->getFaker();

        $adminRoleId = $this
            ->readRow(Role::TABLE_NAME, Role::FIELD_NAME . '=\'' . RolesSeeder::NAME_ADMINS . '\'')[Role::FIELD_ID];
        $userRoleId = $this
            ->readRow(Role::TABLE_NAME, Role::FIELD_NAME . '=\'' . RolesSeeder::NAME_USERS . '\'')[Role::FIELD_ID];

        $this->seedRow(Model::TABLE_NAME, [
            Model::FIELD_ID_ROLE       => $adminRoleId,
            Model::FIELD_TITLE         => $faker->title,
            Model::FIELD_FIRST_NAME    => $faker->firstName,
            Model::FIELD_LAST_NAME     => $faker->lastName,
            Model::FIELD_LANGUAGE      => $faker->languageCode,
            Model::FIELD_EMAIL         => self::ADMIN_EMAIL,
            Model::FIELD_PASSWORD_HASH => $hasher->hash(self::TEST_PASSWORD),
            Model::FIELD_CREATED_AT    => $curDateTime,
        ]);
        $this->seedRow(Model::TABLE_NAME, [
            Model::FIELD_ID_ROLE       => $userRoleId,
            Model::FIELD_TITLE         => $faker->title,
            Model::FIELD_FIRST_NAME    => $faker->firstName,
            Model::FIELD_LAST_NAME     => $faker->lastName,
            Model::FIELD_LANGUAGE      => $faker->languageCode,
            Model::FIELD_EMAIL         => self::USER_EMAIL,
            Model::FIELD_PASSWORD_HASH => $hasher->hash(self::TEST_PASSWORD),
            Model::FIELD_CREATED_AT    => $curDateTime,
        ]);
    }
}
