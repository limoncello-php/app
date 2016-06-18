<?php namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseOnCreate;
use App\Schemes\UserSchema as Schema;

/**
 * @package App
 */
class OnCreate extends BaseOnCreate
{
    /** @inheritdoc */
    const SCHEMA_CLASS = Schema::class;

    /**
     * @inheritdoc
     */
    public function isValidId($index)
    {
        return $index === null;
    }
}
