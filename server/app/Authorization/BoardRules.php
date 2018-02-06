<?php namespace App\Authorization;

use App\Data\Seeds\PassportSeed;
use App\Json\Schemes\BoardSchema as Schema;
use Limoncello\Application\Contracts\Authorization\ResourceAuthorizationRulesInterface;
use Limoncello\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;

/**
 * @package App
 */
class BoardRules implements ResourceAuthorizationRulesInterface
{
    use RulesTrait;

    /** Action name */
    const ACTION_VIEW_BOARDS = 'canViewBoards';

    /** Action name */
    const ACTION_ADMIN_BOARDS = 'canAdminBoards';

    /** Action name */
    const ACTION_VIEW_BOARD_POSTS = 'canViewBoardPosts';

    /**
     * @inheritdoc
     */
    public static function getResourcesType(): string
    {
        return Schema::TYPE;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canViewBoards(ContextInterface $context): bool
    {
        assert($context);

        return true;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canAdminBoards(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_ADMIN_BOARDS);
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canViewBoardPosts(ContextInterface $context): bool
    {
        assert($context);

        return true;
    }
}
