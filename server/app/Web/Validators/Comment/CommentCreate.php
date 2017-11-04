<?php namespace App\Web\Validators\Comment;

use App\Data\Models\Comment as Model;
use App\Validation\CommentRules as r;
use Limoncello\Application\Contracts\Validation\FormRuleSetInterface;

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
