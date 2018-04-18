<?php namespace App\Validation\Role;

use App\Data\Models\Role as Model;
use App\Json\Schemes\RoleSchema as Schema;
use App\Validation\BaseRules;
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
        return self::equals(Schema::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function description(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_DESCRIPTION];

        return self::asSanitizedString(self::stringLengthBetween(1, $maxLength));
    }

    /**
     * @return RuleInterface
     */
    public static function isUniqueRoleId(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_ID];

        return self::asSanitizedString(
            self::stringLengthBetween(1, $maxLength, self::unique(Model::TABLE_NAME, Model::FIELD_ID))
        );
    }
}
