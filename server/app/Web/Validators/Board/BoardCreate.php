<?php namespace App\Web\Validators\Board;

use App\Data\Models\Board as Model;
use App\Validation\BoardRules as r;
use Limoncello\Application\Contracts\Validation\FormRuleSetInterface;

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
