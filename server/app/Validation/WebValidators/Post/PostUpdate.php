<?php namespace App\Validation\WebValidators\Post;

use App\Data\Models\Post as Model;
use App\Validation\Rules\PostRules as r;
use Limoncello\Flute\Contracts\Validation\FormRuleSetInterface;

/**
 * @package App
 */
class PostUpdate implements FormRuleSetInterface
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
