<?php namespace App\Data\Seeds;

use App\Data\Models\User as Model;
use Doctrine\DBAL\DBALException;
use Faker\Generator;
use Limoncello\Contracts\Data\SeedInterface;
use Limoncello\Crypt\Contracts\HasherInterface;
use Limoncello\Data\Seeds\SeedTrait;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class UsersSeed implements SeedInterface
{
    use SeedTrait;

    const NUMBER_OF_RECORDS = 6;

    const DEFAULT_PASSWORD = 'secret';

    /**
     * @inheritdoc
     *
     * @throws DBALException
     */
    public function run(): void
    {
        $this->seedModelsData(self::NUMBER_OF_RECORDS, Model::class, function (ContainerInterface $container) {
            /** @var Generator $faker */
            $faker = $container->get(Generator::class);
            /** @var HasherInterface $hasher */
            $hasher = $container->get(HasherInterface::class);

            $role = $faker->randomElement([
                RolesSeed::ROLE_ADMIN,
                RolesSeed::ROLE_MODERATOR,
                RolesSeed::ROLE_USER,
            ]);

            return [
                Model::FIELD_FIRST_NAME    => $faker->firstName,
                Model::FIELD_LAST_NAME     => $faker->lastName,
                Model::FIELD_EMAIL         => $faker->email,
                Model::FIELD_ID_ROLE       => $role,
                Model::FIELD_PASSWORD_HASH => $hasher->hash(self::DEFAULT_PASSWORD),
                Model::FIELD_CREATED_AT    => $this->now(),
            ];
        });
    }
}
