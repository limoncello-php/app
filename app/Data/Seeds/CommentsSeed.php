<?php namespace App\Data\Seeds;

use App\Data\Models\Comment as Model;
use App\Data\Models\Post;
use App\Data\Models\User;
use Faker\Generator;
use Limoncello\Contracts\Data\SeedInterface;
use Limoncello\Data\Seeds\SeedTrait;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class CommentsSeed implements SeedInterface
{
    use SeedTrait;

    const NUMBER_OF_RECORDS = 800;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->seedModelsData(self::NUMBER_OF_RECORDS, Model::class, function (ContainerInterface $container) {
            /** @var Generator $faker */
            $faker = $container->get(Generator::class);
            $users = $this->readTableData(User::TABLE_NAME);
            $posts = $this->readTableData(Post::TABLE_NAME);

            return [
                Model::FIELD_ID_POST    => $faker->randomElement($posts)[Post::FIELD_ID],
                Model::FIELD_ID_USER    => $faker->randomElement($users)[User::FIELD_ID],
                Model::FIELD_TEXT       => $faker->text(),
                Model::FIELD_CREATED_AT => $this->now(),
            ];
        });
    }
}
