<?php namespace App\Validation\Rules;

use App\Json\Schemes\CommentScheme as Scheme;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class CommentRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function commentType(): RuleInterface
    {
        return self::equals(Scheme::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function text(): RuleInterface
    {
        return self::asSanitizedString();
    }
}
