<?php namespace App\Validation\Role;

use App\Json\Schemas\RoleSchema;
use App\Json\Schemas\UserSchema;
use App\Validation\User\UserRules as r;
use Limoncello\Flute\Contracts\Validation\JsonApiQueryRulesInterface;
use Limoncello\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Settings\ApplicationApi;

/**
 * @package App
 */
class RolesReadUsersQuery implements JsonApiQueryRulesInterface
{
    /**
     * @return RuleInterface[]|null
     */
    public static function getFilterRules(): ?array
    {
        return [
            RoleSchema::REL_USERS . '.' . UserSchema::ATTR_FIRST_NAME => r::asSanitizedString(),
            RoleSchema::REL_USERS . '.' . UserSchema::ATTR_LAST_NAME  => r::asSanitizedString(),
            RoleSchema::REL_USERS . '.' . UserSchema::ATTR_EMAIL      => r::email(),
            RoleSchema::REL_USERS . '.' . UserSchema::ATTR_CREATED_AT => r::asJsonApiDateTime(),
        ];
    }

    /**
     * @return RuleInterface[]|null
     */
    public static function getFieldSetRules(): ?array
    {
        // no field sets are allowed
        return [];
    }

    /**
     * @return RuleInterface|null
     */
    public static function getSortsRule(): ?RuleInterface
    {
        return r::isString(r::inValues([
            RoleSchema::REL_USERS . '.' . UserSchema::ATTR_FIRST_NAME,
            RoleSchema::REL_USERS . '.' . UserSchema::ATTR_LAST_NAME,
            RoleSchema::REL_USERS . '.' . UserSchema::ATTR_EMAIL,
        ]));
    }

    /**
     * @return RuleInterface|null
     */
    public static function getIncludesRule(): ?RuleInterface
    {
        // no includes are allowed
        return r::fail();
    }

    /**
     * @return RuleInterface|null
     */
    public static function getPageOffsetRule(): ?RuleInterface
    {
        // defaults are fine
        return DefaultQueryValidationRules::getPageOffsetRule();
    }

    /**
     * @return RuleInterface|null
     */
    public static function getPageLimitRule(): ?RuleInterface
    {
        // defaults are fine
        return DefaultQueryValidationRules::getPageLimitRuleForDefaultAndMaxSizes(
            ApplicationApi::DEFAULT_PAGE_SIZE,
            ApplicationApi::DEFAULT_MAX_PAGE_SIZE
        );
    }
}
