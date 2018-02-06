<?php namespace App\Authorization;

use App\Api\CommentsApi;
use App\Data\Models\Comment;
use App\Data\Seeds\PassportSeed;
use App\Json\Schemes\CommentSchema as Schema;
use Limoncello\Application\Contracts\Authorization\ResourceAuthorizationRulesInterface;
use Limoncello\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;
use Limoncello\Flute\Contracts\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
        return Schema::TYPE;
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
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canEditComment(ContextInterface $context): bool
    {
        assert(self::reqGetResourceType($context) === Schema::TYPE);

        return
            self::hasScope($context, PassportSeed::SCOPE_ADMIN_MESSAGES) ||
            self::isCurrentUserCommentAuthor($context);
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private static function isCurrentUserCommentAuthor(ContextInterface $context): bool
    {
        $isAuthor = false;

        if (($userId = self::getCurrentUserIdentity($context)) !== null) {
            $userId   = (int)$userId;
            $identity = self::reqGetResourceIdentity($context);

            /** @var Comment|null $comment */
            /** @var FactoryInterface $factory */
            $container = self::ctxGetContainer($context);
            $factory   = $container->get(FactoryInterface::class);
            $comment   = $factory->createApi(CommentsApi::class)->read($identity);
            $isAuthor  = $comment !== null && $comment->{Comment::FIELD_ID_USER} === $userId;
        }

        return $isAuthor;
    }
}
