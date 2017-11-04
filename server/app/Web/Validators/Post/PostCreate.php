<?php namespace App\Web\Validators\Post;

use App\Data\Models\Post as Model;
use App\Validation\PostRules as r;
use Limoncello\Application\Contracts\Validation\FormRuleSetInterface;

/**
 * @package App
 */
class PostCreate implements FormRuleSetInterface
{
    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Model::FIELD_ID_BOARD => r::required(r::boardId()),
            Model::FIELD_TITLE    => r::required(r::title()),
            Model::FIELD_TEXT     => r::required(r::text()),
        ];
    }
}
