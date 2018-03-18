<?php namespace App\Validation\Board;

use App\Data\Models\Board as Model;
use App\Validation\Board\BoardRules as r;
use Limoncello\Flute\Contracts\Validation\FormRulesInterface;

/**
 * @package App
 */
class BoardUpdateForm implements FormRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Model::FIELD_ID      => r::required(r::boardId()),
            Model::FIELD_TITLE   => r::title(),
            Model::FIELD_IMG_URL => r::imgUrl(),
        ];
    }
}
