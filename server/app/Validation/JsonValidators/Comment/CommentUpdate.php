<?php namespace App\Validation\JsonValidators\Comment;

use App\Json\Schemes\CommentSchema as Schema;
use App\Validation\Rules\CommentRules as r;
use Limoncello\Flute\Contracts\Validation\JsonApiRuleSetInterface;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class CommentUpdate implements JsonApiRuleSetInterface
{
    /**
     * @inheritdoc
     */
    public static function getTypeRule(): RuleInterface
    {
        return r::commentType();
    }

    /**
     * @inheritdoc
     */
    public static function getIdRule(): RuleInterface
    {
        return r::commentId();
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Schema::ATTR_TEXT => r::required(r::text()),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToOneRelationshipRules(): array
    {
        // do not allow changing posts for existing comments
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getToManyRelationshipRules(): array
    {
        return [];
    }
}
