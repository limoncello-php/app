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
final class UserCreate implements JsonApiRuleSetInterface
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
        return v::equals(null);
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Scheme::ATTR_FIRST_NAME => v::required(v::firstName()),
            Scheme::ATTR_LAST_NAME  => v::required(v::lastName()),
            Scheme::ATTR_EMAIL      => v::required(v::email()),
            Scheme::V_ATTR_PASSWORD => v::required(v::password()),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToOneRelationshipRules(): array
    {
        return [
            Scheme::REL_ROLE => v::required(v::isRoleRelationship()),
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
