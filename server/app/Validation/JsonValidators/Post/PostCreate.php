<?php namespace App\Validation\JsonValidators\Post;

use App\Json\Schemes\PostSchema as Schema;
use App\Validation\Rules\PostRules as r;
use Limoncello\Flute\Contracts\Validation\JsonApiRuleSetInterface;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class PostCreate implements JsonApiRuleSetInterface
{
    /**
     * @inheritdoc
     */
    public static function getTypeRule(): RuleInterface
    {
        return r::postType();
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
            Schema::ATTR_TITLE => r::required(r::title()),
            Schema::ATTR_TEXT  => r::required(r::text()),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToOneRelationshipRules(): array
    {
        return [
            Schema::REL_BOARD => r::required(r::boardRelationship()),
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
