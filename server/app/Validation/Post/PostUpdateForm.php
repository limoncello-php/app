<?php namespace App\Validation\Post;

use App\Data\Models\Post as Model;
use App\Validation\Post\PostRules as r;
use Limoncello\Flute\Contracts\Validation\FormRulesInterface;

/**
 * @package App
 */
class PostUpdateForm implements FormRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Model::FIELD_ID    => r::required(r::postId()),
            Model::FIELD_TITLE => r::title(),
            Model::FIELD_TEXT  => r::text(),
        ];
    }
}
