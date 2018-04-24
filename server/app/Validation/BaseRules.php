<?php namespace App\Validation;

use App\Api\RolesApi;
use App\Api\UsersApi;
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
     * @return RuleInterface
     */
    public static function roleId(): RuleInterface
    {
        return self::asSanitizedString(self::readable(RolesApi::class));
    }

    /**
     * @return RuleInterface
     */
    public static function roleRelationship(): RuleInterface
    {
        return self::toOneRelationship(RoleSchema::TYPE, static::roleId());
    }

    /**
     * @return RuleInterface
     */
    public static function userId(): RuleInterface
    {
        return self::stringToInt(self::readable(UsersApi::class));
    }

    /**
     * @return RuleInterface
     */
    public static function userRelationship(): RuleInterface
    {
        return self::toOneRelationship(UserSchema::TYPE, static::userId());
    }

    /**
     * @return RuleInterface
     */
    public static function usersRelationship(): RuleInterface
    {
        $readableAll = static::stringArrayToIntArray(static::readableAll(UsersApi::class));

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
