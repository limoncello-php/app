<?php namespace App\Data\Models;

use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Contracts\Data\RelationshipTypes;
use Limoncello\Flute\Types\JsonApiDateTimeType;

/**
 * @package App
 */
class User implements ModelInterface, CommonFields
{
    /** Table name */
    const TABLE_NAME = 'users';

    /** Primary key */
    const FIELD_ID = 'user_id';

    /** Primary key */
    const FIELD_ID_ROLE = Role::FIELD_ID;

    /** Field name */
    const FIELD_EMAIL = 'email';

    /** Field name */
    const FIELD_FIRST_NAME = 'first_name';

    /** Field name */
    const FIELD_LAST_NAME = 'last_name';

    /** Field name */
    const FIELD_PASSWORD_HASH = 'password_hash';

    /** Relationship name */
    const REL_POSTS = 'posts';

    /** Relationship name */
    const REL_ROLE = 'role';

    /** Relationship name */
    const REL_COMMENTS = 'comments';

    /** Minimum password length */
    const MIN_PASSWORD_LENGTH = 5;

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
            self::FIELD_ID            => Type::INTEGER,
            self::FIELD_ID_ROLE       => Type::STRING,
            self::FIELD_FIRST_NAME    => Type::STRING,
            self::FIELD_LAST_NAME     => Type::STRING,
            self::FIELD_EMAIL         => Type::STRING,
            self::FIELD_PASSWORD_HASH => Type::STRING,
            self::FIELD_CREATED_AT    => JsonApiDateTimeType::NAME,
            self::FIELD_UPDATED_AT    => JsonApiDateTimeType::NAME,
            self::FIELD_DELETED_AT    => JsonApiDateTimeType::NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths(): array
    {
        return [
            self::FIELD_ID_ROLE       => 255,
            self::FIELD_FIRST_NAME    => 100,
            self::FIELD_LAST_NAME     => 100,
            self::FIELD_EMAIL         => 255,
            self::FIELD_PASSWORD_HASH => 100,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships(): array
    {
        return [
            RelationshipTypes::BELONGS_TO => [
                self::REL_ROLE => [Role::class, self::FIELD_ID_ROLE, Role::REL_USERS],
            ],
            RelationshipTypes::HAS_MANY   => [
                self::REL_POSTS    => [Post::class, Post::FIELD_ID_USER, Post::REL_USER],
                self::REL_COMMENTS => [Comment::class, Comment::FIELD_ID_USER, Comment::REL_USER],
            ],
        ];
    }
}
