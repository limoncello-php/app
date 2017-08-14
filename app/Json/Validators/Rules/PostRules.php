<?php namespace App\Json\Validators\Rules;

use App\Data\Models\Post as Model;
use App\Json\Schemes\BoardScheme;
use App\Json\Schemes\PostScheme as Scheme;
use Limoncello\Flute\Validation\Rules\ExistInDatabaseTrait;
use Limoncello\Flute\Validation\Rules\RelationshipsTrait;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Limoncello\Validation\Rules;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class PostRules extends Rules
{
    use RelationshipsTrait, ExistInDatabaseTrait;

    /**
     * @return RuleInterface
     */
    public static function isPostType(): RuleInterface
    {
        return self::equals(Scheme::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function isPostId(): RuleInterface
    {
        return self::stringToInt(self::exists(Model::TABLE_NAME, Model::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function isBoardRelationship(): RuleInterface
    {
        return self::required(self::toOneRelationship(BoardScheme::TYPE, BoardRules::isBoardId()));
    }

    /**
     * @return RuleInterface
     */
    public static function title(): RuleInterface
    {
        return self::isString(self::stringLengthMax(Model::getAttributeLengths()[Model::FIELD_TITLE]));
    }

    /**
     * @return RuleInterface
     */
    public static function text(): RuleInterface
    {
        return self::isString();
    }
}
