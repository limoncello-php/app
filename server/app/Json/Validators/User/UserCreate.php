<?php namespace App\Json\Validators\User;

use App\Json\Schemes\UserScheme as Scheme;
use App\Validation\UserRules as r;
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
        return r::userType();
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
            Scheme::ATTR_FIRST_NAME => r::required(r::firstName()),
            Scheme::ATTR_LAST_NAME  => r::required(r::lastName()),
            Scheme::ATTR_EMAIL      => r::required(r::email()),
            Scheme::V_ATTR_PASSWORD => r::required(r::password()),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToOneRelationshipRules(): array
    {
        return [
            Scheme::REL_ROLE => r::required(r::roleRelationship()),
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
