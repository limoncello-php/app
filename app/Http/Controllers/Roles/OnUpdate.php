<?php namespace App\Http\Controllers\Roles;

use App\Http\Controllers\BaseOnUpdate;
use App\Schemes\RoleSchema as Schema;

/**
 * @package App
 */
class OnUpdate extends BaseOnUpdate
{
    /** @inheritdoc */
    const SCHEMA_CLASS = Schema::class;

    /**
     * @inheritdoc
     */
    public function isValidId($index)
    {
        return is_string($index) === true || is_int($index);
    }
}
