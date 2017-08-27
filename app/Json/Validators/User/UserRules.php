<?php namespace App\Json\Validators\User;

use App\Data\Models\User as Model;
use App\Json\Schemes\UserScheme as Scheme;
use App\Json\Validators\AppErrorCodes;
use App\Json\Validators\BaseRules;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class UserRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function userType(): RuleInterface
    {
        return self::equals(Scheme::TYPE);
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
            self::stringLengthMax(
                Model::getAttributeLengths()[Model::FIELD_EMAIL],
                self::filter(FILTER_VALIDATE_EMAIL, null, AppErrorCodes::IS_EMAIL)
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
