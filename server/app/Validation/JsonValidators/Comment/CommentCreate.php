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
final class CommentCreate implements JsonApiRuleSetInterface
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
        return r::equals(null);
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
        return [
            Schema::REL_POST => r::required(r::postRelationship()),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToManyRelationshipRules(): array
    {
        return [];
    }
}
