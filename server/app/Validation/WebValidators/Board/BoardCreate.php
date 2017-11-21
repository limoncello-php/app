<?php namespace App\Validation\WebValidators\Board;

use App\Data\Models\Board as Model;
use App\Validation\Rules\BoardRules as r;
use Limoncello\Flute\Contracts\Validation\FormRuleSetInterface;

/**
 * @package App
 */
class BoardCreate implements FormRuleSetInterface
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
