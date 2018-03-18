<?php namespace App\Validation\Comment;

use App\Data\Models\Comment as Model;
use App\Validation\Comment\CommentRules as r;
use Limoncello\Flute\Contracts\Validation\FormRulesInterface;

/**
 * @package App
 */
class CommentCreateForm implements FormRulesInterface
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
