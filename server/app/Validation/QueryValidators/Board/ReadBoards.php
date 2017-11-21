<?php namespace App\Validation\QueryValidators\Board;

use App\Json\Schemes\BoardScheme as Scheme;
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
            Scheme::ATTR_TITLE      => r::asSanitizedString(),
            Scheme::ATTR_CREATED_AT => r::stringToDateTime(DateBaseType::JSON_API_FORMAT),
        ];
    }
}
