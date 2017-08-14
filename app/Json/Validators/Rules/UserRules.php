<?php namespace App\Json\Validators\Rules;

use App\Data\Models\User as Model;
use App\Json\Schemes\RoleScheme;
use App\Json\Schemes\UserScheme as Scheme;
use Limoncello\Flute\Validation\Rules\ExistInDatabaseTrait;
use Limoncello\Flute\Validation\Rules\RelationshipsTrait;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Limoncello\Validation\Rules;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class UserRules extends Rules
{
    use RelationshipsTrait, ExistInDatabaseTrait;

    /**
     * @return RuleInterface
     */
    public static function isUserType(): RuleInterface
    {
        return self::equals(Scheme::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function isUserId(): RuleInterface
    {
        return self::stringToInt(self::exists(Model::TABLE_NAME, Model::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function isRoleRelationship(): RuleInterface
    {
        return self::toOneRelationship(RoleScheme::TYPE, RoleRules::isRoleId());
    }

    /**
     * @return RuleInterface
     */
    public static function firstName(): RuleInterface
    {
        return self::isString(self::stringLengthMax(Model::getAttributeLengths()[Model::FIELD_FIRST_NAME]));
    }

    /**
     * @return RuleInterface
     */
    public static function lastName(): RuleInterface
    {
        return self::isString(self::stringLengthMax(Model::getAttributeLengths()[Model::FIELD_LAST_NAME]));
    }

    /**
     * @return RuleInterface
     */
    public static function email(): RuleInterface
    {
        return self::isString(
            self::andX(
                self::stringLengthMax(Model::getAttributeLengths()[Model::FIELD_EMAIL]),
                new IsEmailRule()
            )
        );
    }

    /**
     * @return RuleInterface
     */
    public static function password(): RuleInterface
    {
        return self::isString(self::stringLengthMin(Model::MIN_PASSWORD_LENGTH));
    }
}
