<?php namespace App\Database\Models;

use Limoncello\Models\FieldTypes;
use Limoncello\Models\RelationshipTypes;

/**
 * @package App
 */
class Role extends Model
{
    /** @inheritdoc */
    const TABLE_NAME = 'roles';

    /** @inheritdoc */
    const FIELD_ID = 'id_role';

    /** Relationship name */
    const REL_USERS = 'users';

    /** Field name */
    const FIELD_NAME = 'name';

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes()
    {
        return [
            self::FIELD_ID         => FieldTypes::INT,
            self::FIELD_NAME       => FieldTypes::STRING,
            self::FIELD_CREATED_AT => FieldTypes::DATE,
            self::FIELD_UPDATED_AT => FieldTypes::DATE,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths()
    {
        return [
            self::FIELD_NAME => 255,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships()
    {
        return [
            RelationshipTypes::HAS_MANY => [
                self::REL_USERS => [User::class, User::FIELD_ID_ROLE, User::REL_ROLE],
            ],
        ];
    }
}
