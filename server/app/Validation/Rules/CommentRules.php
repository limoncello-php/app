<?php namespace App\Validation\Rules;

use App\Json\Schemes\CommentSchema as Schema;
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
        return self::equals(Schema::TYPE);
    }

    /**
     * @return RuleInterface
     */
    public static function text(): RuleInterface
    {
        return self::asSanitizedString();
    }
}
