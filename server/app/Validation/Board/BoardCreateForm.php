<?php namespace App\Validation\Board;

use App\Data\Models\Board as Model;
use App\Validation\Board\BoardRules as r;
use Limoncello\Flute\Contracts\Validation\FormRulesInterface;

/**
 * @package App
 */
class BoardCreateForm implements FormRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Model::FIELD_TITLE   => r::required(r::title()),
            Model::FIELD_IMG_URL => r::required(r::imgUrl()),
        ];
    }
}
