<?php namespace App\Web\Validators\Comment;

use App\Data\Models\Comment as Model;
use App\Validation\CommentRules as r;
use Limoncello\Application\Contracts\Validation\FormRuleSetInterface;

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
