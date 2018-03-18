<?php namespace App\Validation\Post;

use App\Data\Models\Post as Model;
use App\Validation\Post\PostRules as r;
use Limoncello\Flute\Contracts\Validation\FormRulesInterface;

/**
 * @package App
 */
class PostCreateForm implements FormRulesInterface
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
