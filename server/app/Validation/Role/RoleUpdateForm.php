<?php namespace App\Validation\Role;

use App\Json\Schemes\RoleSchema as Schema;
use App\Validation\Role\RoleRules as r;
use Limoncello\Flute\Contracts\Validation\FormRulesInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class RoleUpdateForm implements FormRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Schema::ATTR_DESCRIPTION => r::required(r::description()),
        ];
    }
}
