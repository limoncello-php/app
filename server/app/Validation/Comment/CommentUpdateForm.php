<?php namespace App\Validation\Comment;

use App\Data\Models\Comment as Model;
use App\Validation\Comment\CommentRules as r;
use Limoncello\Flute\Contracts\Validation\FormRulesInterface;

/**
 * @package App
 */
class CommentUpdateForm implements FormRulesInterface
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
