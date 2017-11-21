<?php namespace App\Validation\WebValidators\Comment;

use App\Data\Models\Comment as Model;
use App\Validation\Rules\CommentRules as r;
use Limoncello\Flute\Contracts\Validation\FormRuleSetInterface;

/**
 * @package App
 */
class CommentUpdate implements FormRuleSetInterface
{
    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Model::FIELD_ID   => r::required(r::commentId()),
            Model::FIELD_TEXT => r::required(r::text()),
        ];
    }
}
