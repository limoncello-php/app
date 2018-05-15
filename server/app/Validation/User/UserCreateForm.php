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
final class UserCreateForm implements FormRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Schema::ATTR_FIRST_NAME => r::required(r::firstName()),
            Schema::ATTR_LAST_NAME  => r::required(r::lastName()),
            Schema::ATTR_EMAIL      => r::required(r::uniqueEmail()),
            Schema::REL_ROLE        => r::required(r::roleId()),

            Schema::CAPTURE_NAME_PASSWORD              => r::required(r::password()),
            Schema::CAPTURE_NAME_PASSWORD_CONFIRMATION => r::required(r::password()),

            CsrfSettings::DEFAULT_HTTP_REQUEST_CSRF_TOKEN_KEY => r::required(r::isString()),
        ];
    }
}
