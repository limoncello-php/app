<?php namespace App\Authorization;

use App\Data\Models\Comment;
use App\Json\Api\CommentsApi;
use App\Json\Schemes\CommentScheme as Scheme;
use Limoncello\Application\Contracts\Authorization\ResourceAuthorizationRulesInterface;
use Limoncello\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;
use Limoncello\Flute\Contracts\FactoryInterface;
use Settings\Passport;

/**
 * @package App
 */
class CommentRules implements ResourceAuthorizationRulesInterface
{
    use RulesTrait;

    /** Action name */
    const ACTION_VIEW_COMMENTS = 'canViewComments';

    /** Action name */
    const ACTION_CREATE_COMMENT = 'canCreateComment';

    /** Action name */
    const ACTION_EDIT_COMMENT = 'canEditComment';

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
    public static function canViewComments(ContextInterface $context): bool
    {
        assert($context);

        return true;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canCreateComment(ContextInterface $context): bool
    {
        $userId = self::getCurrentUserIdentity($context);

        return $userId !== null;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canEditComment(ContextInterface $context): bool
    {
        assert(self::reqGetResourceType($context) === Scheme::TYPE);

        return
            self::hasScope($context, Passport::SCOPE_ADMIN_MESSAGES) ||
            self::isCurrentUserCommentAuthor($context);
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    private static function isCurrentUserCommentAuthor(ContextInterface $context): bool
    {
        $isAuthor = false;

        if (($userId = self::getCurrentUserIdentity($context)) !== null) {
            $identity = self::reqGetResourceIdentity($context);

            /** @var CommentsApi $api */
            /** @var Comment|null $comment */
            /** @var FactoryInterface $factory */
            $container = self::ctxGetContainer($context);
            $factory   = $container->get(FactoryInterface::class);
            $api       = $factory->createApi(CommentsApi::class);
            $comment   = $api->readResource($identity);
            $isAuthor  = $comment !== null && $comment->{Comment::FIELD_ID_USER} === $userId;
        }

        return $isAuthor;
    }
}
