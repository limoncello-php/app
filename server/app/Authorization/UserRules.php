<?php namespace App\Authorization;

use App\Data\Seeds\PassportSeed;
use App\Json\Schemes\UserScheme as Scheme;
use Limoncello\Application\Contracts\Authorization\ResourceAuthorizationRulesInterface;
use Limoncello\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;

/**
 * @package App
 */
class UserRules implements ResourceAuthorizationRulesInterface
{
    use RulesTrait;

    /** Action name */
    const ACTION_VIEW_USERS = 'canViewUsers';

    /** Action name */
    const ACTION_MANAGE_USERS = 'canManageUsers';

    /** Action name */
    const ACTION_VIEW_USER_POSTS = 'canViewUserPosts';

    /** Action name */
    const ACTION_VIEW_USER_COMMENTS = 'canViewUserComments';

    /**
     * @inheritdoc
     */
    public static function getResourcesType(): string
    {
        return Scheme::TYPE;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canViewUsers(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_ADMIN_USERS);
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canManageUsers(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_ADMIN_USERS);
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canViewUserPosts(ContextInterface $context): bool
    {
        return self::ctxHasCurrentAccount($context);
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canViewUserComments(ContextInterface $context): bool
    {
        return self::ctxHasCurrentAccount($context);
    }
}
