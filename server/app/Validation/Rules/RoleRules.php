<?php namespace App\Validation\Rules;

use App\Data\Models\Role as Model;
use App\Json\Schemes\RoleScheme as Scheme;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class RoleRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function roleType(): RuleInterface
    {
        return self::equals(Scheme::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function description(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_DESCRIPTION];

        return self::asSanitizedString(self::stringLengthMax($maxLength));
    }

    /**
     * @return RuleInterface
     */
    public static function isUniqueRoleId(): RuleInterface
    {
        return self::asSanitizedString(self::unique(Model::TABLE_NAME, Model::FIELD_ID));
    }
}
