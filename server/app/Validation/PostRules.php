<?php namespace App\Validation;

use App\Data\Models\Post as Model;
use App\Json\Schemes\PostScheme as Scheme;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class PostRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function postType(): RuleInterface
    {
        return self::equals(Scheme::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function title(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_TITLE];

        return self::asSanitizedString(self::stringLengthMax($maxLength));
    }

    /**
     * @return RuleInterface
     */
    public static function text(): RuleInterface
    {
        return self::asSanitizedString();
    }
}
