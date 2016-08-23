<?php namespace App\Database\Seeds\Testing;

use App\Database\Models\Board;
use App\Database\Models\Post as Model;
use App\Database\Models\User;
use App\Database\Seeds\Seeder;

/**
 * @package App
 */
class PostsSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        $curDateTime = $this->getCurrentDateTime();

        $faker  = $this->getFaker();
        $users  = $this->readTable(User::TABLE_NAME);
        $boards = $this->readTable(Board::TABLE_NAME);
        $this->seedTable(100, Model::TABLE_NAME, function () use ($faker, $curDateTime, $users, $boards) {
            return [
                Model::FIELD_ID_BOARD   => $faker->randomElement($boards)[Board::FIELD_ID],
                Model::FIELD_ID_USER    => $faker->randomElement($users)[User::FIELD_ID],
                Model::FIELD_TITLE      => $faker->text(50),
                Model::FIELD_TEXT       => $faker->text(),
                Model::FIELD_CREATED_AT => $curDateTime,
            ];
        });
    }
}
