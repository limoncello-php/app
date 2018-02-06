<?php namespace App\Validation\JsonValidators\Board;

use App\Json\Schemes\BoardSchema as Schema;
use App\Validation\Rules\BoardRules as r;
use Limoncello\Flute\Contracts\Validation\JsonApiRuleSetInterface;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class BoardUpdate implements JsonApiRuleSetInterface
{
    /**
     * @inheritdoc
     */
    public static function getTypeRule(): RuleInterface
    {
        return r::boardType();
    }

    /**
     * @inheritdoc
     */
    public static function getIdRule(): RuleInterface
    {
        return r::boardId();
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Schema::ATTR_TITLE   => r::title(),
            Schema::ATTR_IMG_URL => r::imgUrl(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToOneRelationshipRules(): array
    {
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
