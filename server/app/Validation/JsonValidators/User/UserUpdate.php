<?php namespace App\Validation\JsonValidators\User;

use App\Json\Schemes\UserSchema as Schema;
use App\Validation\Rules\UserRules as r;
use Limoncello\Flute\Contracts\Validation\JsonApiRuleSetInterface;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class UserUpdate implements JsonApiRuleSetInterface
{
    /**
     * @inheritdoc
     */
    public static function getTypeRule(): RuleInterface
    {
        return r::userType();
    }

    /**
     * @inheritdoc
     */
    public static function getIdRule(): RuleInterface
    {
        return r::userId();
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Schema::ATTR_FIRST_NAME => r::firstName(),
            Schema::ATTR_LAST_NAME  => r::lastName(),
            Schema::ATTR_EMAIL      => r::email(),
            Schema::V_ATTR_PASSWORD => r::password(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToOneRelationshipRules(): array
    {
        return [
            Schema::REL_ROLE => r::roleRelationship(),
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
