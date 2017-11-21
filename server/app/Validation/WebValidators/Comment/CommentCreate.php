<?php namespace App\Validation\WebValidators\Comment;

use App\Data\Models\Comment as Model;
use App\Validation\Rules\CommentRules as r;
use Limoncello\Flute\Contracts\Validation\FormRuleSetInterface;

/**
 * @package App
 */
class CommentCreate implements FormRuleSetInterface
{
    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Model::FIELD_ID_POST => r::required(r::postId()),
            Model::FIELD_TEXT    => r::required(r::text()),
        ];
    }
}
