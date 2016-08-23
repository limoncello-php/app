<?php namespace App\Database\Seeds\Testing;

use App\Database\Models\Comment as Model;
use App\Database\Models\Post;
use App\Database\Models\User;
use App\Database\Seeds\Seeder;

/**
 * @package App
 */
class CommentsSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        $curDateTime = $this->getCurrentDateTime();

        $faker  = $this->getFaker();
        $users  = $this->readTable(User::TABLE_NAME);
        $posts  = $this->readTable(Post::TABLE_NAME);
        $this->seedTable(400, Model::TABLE_NAME, function () use ($faker, $curDateTime, $users, $posts) {
            return [
                Model::FIELD_ID_POST    => $faker->randomElement($posts)[Post::FIELD_ID],
                Model::FIELD_ID_USER    => $faker->randomElement($users)[User::FIELD_ID],
                Model::FIELD_TEXT       => $faker->text(),
                Model::FIELD_CREATED_AT => $curDateTime,
            ];
        });
    }
}
