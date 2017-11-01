<?php namespace App\Json\Validators\Role;

use App\Data\Models\Role as Model;
use App\Json\Schemes\RoleScheme as Scheme;
use App\Json\Validators\BaseRules;
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

        return self::isSanitizedString(self::stringLengthMax($maxLength));
    }

    /**
     * @return RuleInterface
     */
    public static function isUniqueRoleId(): RuleInterface
    {
        return self::isSanitizedString(self::unique(Model::TABLE_NAME, Model::FIELD_ID));
    }
}
