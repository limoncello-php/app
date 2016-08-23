<?php namespace App\Database\Seeds\Testing;

use App\Database\Models\Board as Model;
use App\Database\Seeds\Seeder;

/**
 * @package App
 */
class BoardsSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        $curDateTime = $this->getCurrentDateTime();

        $faker = $this->getFaker();
        $this->seedTable(10, Model::TABLE_NAME, function () use ($faker, $curDateTime) {
            return [
                Model::FIELD_TITLE      => 'Board ' . $faker->text(20),
                Model::FIELD_CREATED_AT => $curDateTime,
            ];
        });
    }
}
