<?php namespace App\Data\Seeds;

use App\Data\Models\Board as Model;
use Limoncello\Contracts\Data\SeedInterface;
use Limoncello\Data\Seeds\SeedTrait;

/**
 * @package App
 */
class BoardsSeed implements SeedInterface
{
    use SeedTrait;

    /**
     * @inheritdoc
     */
    public function run(): void
    {
        $data = [
            [Model::FIELD_TITLE => 'Regions', Model::FIELD_IMG_URL => 'img/regions.jpg'],
            [Model::FIELD_TITLE => 'Politics', Model::FIELD_IMG_URL => 'img/politics.jpg'],
            [Model::FIELD_TITLE => 'Money', Model::FIELD_IMG_URL => 'img/money.jpg'],
            [Model::FIELD_TITLE => 'Entertainment', Model::FIELD_IMG_URL => 'img/entertainment.jpg'],
            [Model::FIELD_TITLE => 'Tech', Model::FIELD_IMG_URL => 'img/tech.jpg'],
            [Model::FIELD_TITLE => 'Sport', Model::FIELD_IMG_URL => 'img/sport.jpg'],
            [Model::FIELD_TITLE => 'Travel', Model::FIELD_IMG_URL => 'img/travel.jpg'],
            [Model::FIELD_TITLE => 'Style', Model::FIELD_IMG_URL => 'img/style.jpg'],
            [Model::FIELD_TITLE => 'Health', Model::FIELD_IMG_URL => 'img/health.jpg'],
        ];

        $now = $this->now();
        foreach ($data as $attributes) {
            $this->seedModelData(Model::class, $attributes + [Model::FIELD_CREATED_AT => $now]);
        }
    }
}
