<?php namespace App\Validation\QueryValidators\Board;

use App\Json\Schemes\BoardSchema as Schema;
use App\Validation\Rules\BoardRules as r;
use Limoncello\Flute\Contracts\Validation\QueryRuleSetInterface;
use Limoncello\Flute\Types\DateBaseType;

/**
 * @package App
 */
class ReadBoards implements QueryRuleSetInterface
{
    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Schema::ATTR_TITLE      => r::asSanitizedString(),
            Schema::ATTR_CREATED_AT => r::stringToDateTime(DateBaseType::JSON_API_FORMAT),
        ];
    }
}
