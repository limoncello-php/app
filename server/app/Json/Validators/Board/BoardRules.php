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
        $maxLength = Model::getAttributeLengths()[Model::FIELD_TITLE];

        return self::isString(self::sanitizeText(self::stringLengthMax($maxLength, self::unique(
            Model::TABLE_NAME,
            Model::FIELD_TITLE
        ))));
    }

    /**
     * @return RuleInterface
     */
    public static function imgUrl(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_IMG_URL];

        return self::isString(self::sanitizeUrl(self::stringLengthMax($maxLength)));
    }
}
