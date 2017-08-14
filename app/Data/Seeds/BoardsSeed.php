<?php namespace App\Data\Seeds;

use App\Data\Models\Board as Model;
use Faker\Generator;
use Limoncello\Contracts\Data\SeedInterface;
use Limoncello\Data\Seeds\SeedTrait;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class BoardsSeed implements SeedInterface
{
    use SeedTrait;

    const NUMBER_OF_RECORDS = 10;

    /**
     * @inheritdoc
     */
    public function run(): void
    {
        $this->seedModelsData(self::NUMBER_OF_RECORDS, Model::class, function (ContainerInterface $container) {
            /** @var Generator $faker */
            $faker = $container->get(Generator::class);

            return [
                Model::FIELD_TITLE      => 'Board ' . $faker->text(20),
                Model::FIELD_CREATED_AT => $this->now(),
            ];
        });
    }
}
