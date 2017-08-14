<?php namespace App\Data\Models;

use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Contracts\Data\RelationshipTypes;
use Limoncello\Flute\Types\JsonApiDateTimeType;

/**
 * @package App
 */
class Role implements ModelInterface, CommonFields
{
    /** Table name */
    const TABLE_NAME = 'roles';

    /** Primary key */
    const FIELD_ID = 'id_role';

    /** Field name */
    const FIELD_DESCRIPTION = 'description';

    /** Relationship name */
    const REL_USERS = 'users';

    /**
     * @inheritdoc
     */
    public static function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyName(): string
    {
        return static::FIELD_ID;
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes(): array
    {
        return [
            self::FIELD_ID          => Type::STRING,
            self::FIELD_DESCRIPTION => Type::STRING,
            self::FIELD_CREATED_AT  => JsonApiDateTimeType::NAME,
            self::FIELD_UPDATED_AT  => JsonApiDateTimeType::NAME,
            self::FIELD_DELETED_AT  => JsonApiDateTimeType::NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths(): array
    {
        return [
            self::FIELD_ID          => 255,
            self::FIELD_DESCRIPTION => 255,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships(): array
    {
        return [
            RelationshipTypes::HAS_MANY => [
                self::REL_USERS => [User::class, User::FIELD_ID_ROLE, User::REL_ROLE],
            ],
        ];
    }
}
