<?php namespace App\Validation\User;

use App\Json\Schemas\UserSchema as Schema;
use App\Validation\User\UserRules as r;
use Limoncello\Application\Packages\Csrf\CsrfSettings;
use Limoncello\Flute\Contracts\Validation\FormRulesInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class UserUpdateForm implements FormRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Schema::ATTR_FIRST_NAME => r::firstName(),
            Schema::ATTR_LAST_NAME  => r::lastName(),
            Schema::REL_ROLE        => r::roleId(),


            Schema::CAPTURE_NAME_PASSWORD              => r::orX(r::equals(''), r::password()),
            Schema::CAPTURE_NAME_PASSWORD_CONFIRMATION => r::orX(r::equals(''), r::password()),

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => r::required(r::isString()),
        ];
    }
}
