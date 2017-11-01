<?php namespace App\Json\Validators\Post;

use App\Data\Models\Post as Model;
use App\Json\Schemes\PostScheme as Scheme;
use App\Json\Validators\BaseRules;
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

        return self::isString(self::sanitizeText(self::stringLengthMax($maxLength)));
    }

    /**
     * @return RuleInterface
     */
    public static function text(): RuleInterface
    {
        return self::isString(self::sanitizeText());
    }
}
