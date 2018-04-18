<?php namespace App\Validation;

use App\Data\Models\Role;
use App\Data\Models\User;
use App\Json\Schemes\RoleSchema;
use App\Json\Schemes\UserSchema;
use Limoncello\Flute\Types\DateTime;
use Limoncello\Flute\Validation\JsonApi\Rules\ExistInDatabaseTrait;
use Limoncello\Flute\Validation\JsonApi\Rules\RelationshipsTrait;
use Limoncello\Validation\Contracts\Errors\ErrorCodes;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Limoncello\Validation\Rules;

/**
 * @package App
 */
class BaseRules extends Rules
{
    use RelationshipsTrait, ExistInDatabaseTrait;

    /**
     * @return RuleInterface
     */
    public static function roleId(): RuleInterface
    {
        return self::asSanitizedString(self::exists(Role::TABLE_NAME, Role::FIELD_ID));
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
        return self::stringToInt(self::exists(User::TABLE_NAME, User::FIELD_ID));
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
        $readableAll = static::stringArrayToIntArray(static::existAll(User::TABLE_NAME, User::FIELD_ID));

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
