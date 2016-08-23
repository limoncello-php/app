<?php namespace App\Database\Models;

use App\Database\Types\DateTimeType;
use Doctrine\DBAL\Types\Type;
use Limoncello\JsonApi\Models\RelationshipTypes;

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
            self::FIELD_ID         => Type::INTEGER,
            self::FIELD_NAME       => Type::STRING,
            self::FIELD_CREATED_AT => DateTimeType::NAME,
            self::FIELD_UPDATED_AT => DateTimeType::NAME,
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
