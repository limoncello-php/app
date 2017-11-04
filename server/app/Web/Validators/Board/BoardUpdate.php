<?php namespace App\Web\Validators\Board;

use App\Data\Models\Board as Model;
use App\Validation\BoardRules as r;
use Limoncello\Application\Contracts\Validation\FormRuleSetInterface;

/**
 * @package App
 */
class BoardUpdate implements FormRuleSetInterface
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
