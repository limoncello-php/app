<?php namespace App\Json\Validators\Board;

use App\Data\Models\Board as Model;
use App\Json\Schemes\BoardScheme as Scheme;
use App\Json\Validators\BaseRules;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class BoardRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function boardType(): RuleInterface
    {
        return self::equals(Scheme::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function title(): RuleInterface
    {
        return self::isString(self::stringLengthMax(Model::getAttributeLengths()[Model::FIELD_TITLE]));
    }
}
