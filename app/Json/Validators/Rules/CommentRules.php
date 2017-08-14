<?php namespace App\Json\Validators\Rules;

use App\Data\Models\Comment as Model;
use App\Json\Schemes\CommentScheme as Scheme;
use App\Json\Schemes\PostScheme;
use Limoncello\Flute\Validation\Rules\ExistInDatabaseTrait;
use Limoncello\Flute\Validation\Rules\RelationshipsTrait;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Limoncello\Validation\Rules;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class CommentRules extends Rules
{
    use RelationshipsTrait, ExistInDatabaseTrait;

    /**
     * @return RuleInterface
     */
    public static function isCommentType(): RuleInterface
    {
        return self::equals(Scheme::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function isCommentId(): RuleInterface
    {
        return self::stringToInt(self::exists(Model::TABLE_NAME, Model::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function isPostRelationship(): RuleInterface
    {
        return self::required(self::toOneRelationship(PostScheme::TYPE, PostRules::isPostId()));
    }

    /**
     * @return RuleInterface
     */
    public static function text(): RuleInterface
    {
        return self::isString();
    }
}
