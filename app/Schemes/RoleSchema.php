<?php namespace App\Schemes;

use App\Database\Models\Role as Model;

/**
 * @package App
 */
class RoleSchema extends BaseSchema
{
    /** Type */
    const TYPE = 'roles';

    /** Model class name */
    const MODEL = Model::class;

    /** Attribute name */
    const ATTR_NAME = 'name';

    /** Relationship name */
    const REL_USERS = 'users';

    /**
     * @inheritdoc
     */
    public static function getMappings()
    {
        return [
            self::SCHEMA_ATTRIBUTES => [
                self::ATTR_NAME       => Model::FIELD_NAME,
                self::ATTR_CREATED_AT => Model::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT => Model::FIELD_UPDATED_AT,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_USERS => Model::REL_USERS,
            ],
        ];
    }
}
