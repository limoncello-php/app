<?php namespace App\Json\Validators;

use App\Json\Schemes\UserScheme as Scheme;
use App\Json\Validators\Rules\UserRules as v;
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
        return v::isUserType();
    }

    /**
     * @inheritdoc
     */
    public static function getIdRule(): RuleInterface
    {
        return v::isUserId();
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Scheme::ATTR_FIRST_NAME => v::firstName(),
            Scheme::ATTR_LAST_NAME  => v::lastName(),
            Scheme::ATTR_EMAIL      => v::email(),
            Scheme::V_ATTR_PASSWORD => v::password(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToOneRelationshipRules(): array
    {
        return [
            Scheme::REL_ROLE => v::isRoleRelationship(),
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
