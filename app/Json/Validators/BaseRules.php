<?php namespace App\Json\Validators;

use App\Data\Models\Board;
use App\Data\Models\Comment;
use App\Data\Models\Post;
use App\Data\Models\Role;
use App\Data\Models\User;
use App\Json\Schemes\BoardScheme;
use App\Json\Schemes\CommentScheme;
use App\Json\Schemes\PostScheme;
use App\Json\Schemes\RoleScheme;
use App\Json\Schemes\UserScheme;
use Limoncello\Flute\Validation\Rules\ExistInDatabaseTrait;
use Limoncello\Flute\Validation\Rules\RelationshipsTrait;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Limoncello\Validation\Rules;

/**
 * @package App
 */
class BaseRules extends Rules
{
    use RelationshipsTrait, ExistInDatabaseTrait;

    /**
     * @return RuleInterface
     */
    public static function boardId(): RuleInterface
    {
        return self::stringToInt(self::exists(Board::TABLE_NAME, Board::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function boardRelationship(): RuleInterface
    {
        return self::toOneRelationship(BoardScheme::TYPE, static::boardId());
    }

    /**
     * @return RuleInterface
     */
    public static function commentId(): RuleInterface
    {
        return self::stringToInt(self::exists(Comment::TABLE_NAME, Comment::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function commentRelationship(): RuleInterface
    {
        return self::toOneRelationship(CommentScheme::TYPE, static::commentId());
    }

    /**
     * @return RuleInterface
     */
    public static function postId(): RuleInterface
    {
        return self::stringToInt(self::exists(Post::TABLE_NAME, Post::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function postRelationship(): RuleInterface
    {
        return self::toOneRelationship(PostScheme::TYPE, static::postId());
    }

    /**
     * @return RuleInterface
     */
    public static function roleId(): RuleInterface
    {
        return self::isString(self::exists(Role::TABLE_NAME, Role::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function roleRelationship(): RuleInterface
    {
        return self::toOneRelationship(RoleScheme::TYPE, static::roleId());
    }

    /**
     * @return RuleInterface
     */
    public static function userId(): RuleInterface
    {
        return self::stringToInt(self::exists(User::TABLE_NAME, User::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function userRelationship(): RuleInterface
    {
        return self::toOneRelationship(UserScheme::TYPE, static::userId());
    }

    /**
     * @return RuleInterface
     */
    public static function usersRelationship(): RuleInterface
    {
        $readableAll = static::stringArrayToIntArray(static::existAll(User::TABLE_NAME, User::FIELD_ID));

        return self::toManyRelationship(UserScheme::TYPE, $readableAll);
    }
}
