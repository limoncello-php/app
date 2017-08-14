<?php namespace App\Json\Validators\Rules;

use App\Data\Models\Board as Model;
use App\Json\Schemes\BoardScheme as Scheme;
use Limoncello\Flute\Validation\Rules\ExistInDatabaseTrait;
use Limoncello\Flute\Validation\Rules\RelationshipsTrait;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Limoncello\Validation\Rules;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class BoardRules extends Rules
{
    use RelationshipsTrait, ExistInDatabaseTrait;

    /**
     * @return RuleInterface
     */
    public static function isBoardType(): RuleInterface
    {
        return self::equals(Scheme::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function isBoardId(): RuleInterface
    {
        return self::stringToInt(self::exists(Model::TABLE_NAME, Model::FIELD_ID));
    }

    /**
     * @return RuleInterface
     */
    public static function title(): RuleInterface
    {
        return self::isString(self::stringLengthMax(Model::getAttributeLengths()[Model::FIELD_TITLE]));
    }
}
