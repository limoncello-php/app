<?php namespace App\Json\Validators\Rules;

use App\Data\Models\Role as Model;
use App\Json\Schemes\RoleScheme as Scheme;
use Limoncello\Flute\Validation\Rules\ExistInDatabaseTrait;
use Limoncello\Flute\Validation\Rules\RelationshipsTrait;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Limoncello\Validation\Rules;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class RoleRules extends Rules
{
    use RelationshipsTrait, ExistInDatabaseTrait;

    /**
     * @return RuleInterface
     */
    public static function isRoleType(): RuleInterface
    {
        return self::equals(Scheme::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function isRoleId(): RuleInterface
    {
        return self::isString(self::exists(Model::TABLE_NAME, Model::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function isUniqueRoleId(): RuleInterface
    {
        return self::isString(self::unique(Model::TABLE_NAME, Model::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function description(): RuleInterface
    {
        return self::isString(self::stringLengthMax(Model::getAttributeLengths()[Model::FIELD_DESCRIPTION]));
    }
}
