<?php namespace App\Validation;

use App\Api\RolesApi;
use App\Api\UsersApi;
use App\Data\Models\Role;
use App\Json\Schemas\RoleSchema;
use App\Json\Schemas\UserSchema;
use Limoncello\Flute\Types\DateTime;
use Limoncello\Flute\Validation\Rules\ApiRulesTrait;
use Limoncello\Flute\Validation\Rules\DatabaseRulesTrait;
use Limoncello\Flute\Validation\Rules\RelationshipRulesTrait;
use Limoncello\Validation\Contracts\Errors\ErrorCodes;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Limoncello\Validation\Rules;

/**
 * @package App
 */
class BaseRules extends Rules
{
    use RelationshipRulesTrait, DatabaseRulesTrait, ApiRulesTrait;

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function roleId(RuleInterface $next = null): RuleInterface
    {
        $maxLength = Role::getAttributeLengths()[Role::FIELD_ID];

        return self::asSanitizedString(self::stringLengthMax($maxLength, self::readable(RolesApi::class, $next)));
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function roleRelationship(RuleInterface $next = null): RuleInterface
    {
        return self::toOneRelationship(RoleSchema::TYPE, static::roleId($next));
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function userId(RuleInterface $next = null): RuleInterface
    {
        return self::stringToInt(self::readable(UsersApi::class, $next));
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function userRelationship(RuleInterface $next = null): RuleInterface
    {
        return self::toOneRelationship(UserSchema::TYPE, static::userId($next));
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function usersRelationship(RuleInterface $next = null): RuleInterface
    {
        $readableAll = static::stringArrayToIntArray(static::readableAll(UsersApi::class, $next));

        return self::toManyRelationship(UserSchema::TYPE, $readableAll);
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function asSanitizedUrl(RuleInterface $next = null): RuleInterface
    {
        return self::isString(self::filter(FILTER_SANITIZE_URL, null, ErrorCodes::INVALID_VALUE, $next));
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function asSanitizedString(RuleInterface $next = null): RuleInterface
    {
        return self::isString(
            self::filter(FILTER_SANITIZE_FULL_SPECIAL_CHARS, null, ErrorCodes::INVALID_VALUE, $next)
        );
    }

    /**
     * @param RuleInterface|null $next
     *
     * @return RuleInterface
     */
    public static function asJsonApiDateTime(RuleInterface $next = null): RuleInterface
    {
        return self::stringToDateTime(DateTime::JSON_API_FORMAT, $next);
    }
}
