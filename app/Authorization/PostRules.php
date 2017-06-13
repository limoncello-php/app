<?php namespace App\Authorization;

use App\Data\Models\Post;
use App\Json\Api\PostsApi;
use App\Json\Schemes\PostScheme as Scheme;
use Limoncello\Application\Contracts\Authorization\ResourceAuthorizationRulesInterface;
use Limoncello\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;
use Limoncello\Flute\Contracts\FactoryInterface;
use Settings\Passport;

/**
 * @package App
 */
class PostRules implements ResourceAuthorizationRulesInterface
{
    use RulesTrait;

    /** Action name */
    const ACTION_VIEW_POSTS = 'canViewPosts';

    /** Action name */
    const ACTION_CREATE_POST = 'canCreatePost';

    /** Action name */
    const ACTION_EDIT_POST = 'canEditPost';

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
    public static function canViewPosts(ContextInterface $context): bool
    {
        assert($context);

        return true;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canCreatePost(ContextInterface $context): bool
    {
        $userId = self::getCurrentUserIdentity($context);

        return $userId !== null;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    public static function canEditPost(ContextInterface $context): bool
    {
        assert(self::reqGetResourceType($context) === Scheme::TYPE);

        return
            self::hasScope($context, Passport::SCOPE_ADMIN_MESSAGES) ||
            self::isCurrentUserPostAuthor($context);
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     */
    private static function isCurrentUserPostAuthor(ContextInterface $context): bool
    {
        $isAuthor = false;

        if (($userId = self::getCurrentUserIdentity($context)) !== null) {
            $identity = self::reqGetResourceIdentity($context);

            /** @var PostsApi $api */
            /** @var FactoryInterface $factory */
            $container = self::ctxGetContainer($context);
            $factory   = $container->get(FactoryInterface::class);
            $api       = $factory->createApi(PostsApi::class);
            $post      = $api->readResource($identity);
            $isAuthor  = $post !== null && $post->{Post::FIELD_ID_USER} === $userId;
        }

        return $isAuthor;
    }
}
