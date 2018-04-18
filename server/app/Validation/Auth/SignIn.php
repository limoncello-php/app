<?php namespace App\Validation\Auth;

use App\Validation\User\UserRules as r;
use App\Web\Controllers\AuthController;
use Limoncello\Flute\Contracts\Validation\FormRulesInterface;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App\Web
 */
class SignIn implements FormRulesInterface
{
    /**
     * @return RuleInterface[]
     */
    public static function getAttributeRules(): array
    {
        return [
            AuthController::FORM_EMAIL    => r::required(r::email()),
            AuthController::FORM_PASSWORD => r::required(r::password()),
            AuthController::FORM_REMEMBER => r::stringToBool(),
        ];
    }
}
