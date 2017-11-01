<?php namespace App\Authorization;

use Limoncello\Application\Authorization\AuthorizationRulesTrait;
use Limoncello\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;
use Limoncello\Passport\Contracts\Authentication\PassportAccountInterface;

/**
 * @package App
 */
trait RulesTrait
{
    use AuthorizationRulesTrait;

    /**
     * @param ContextInterface $context
     * @param string           $scope
     *
     * @return bool
     */
    protected static function hasScope(ContextInterface $context, string $scope): bool
    {
        $result = false;

        if (static::ctxHasCurrentAccount($context) === true) {
            /** @var PassportAccountInterface $account */
            $account = self::ctxGetCurrentAccount($context);
            $result  = $account->hasScope($scope);
        }

        return $result;
    }

    /**
     * @param ContextInterface $context
     *
     * @return int|string|null
     */
    protected static function getCurrentUserIdentity(ContextInterface $context)
    {
        $userId = null;

        /** @var PassportAccountInterface $account */
        if (self::ctxHasCurrentAccount($context) === true &&
            ($account = self::ctxGetCurrentAccount($context)) !== null &&
            $account->hasUserIdentity() === true
        ) {
            $userId = $account->getUserIdentity();
        }

        return $userId;
    }
}
