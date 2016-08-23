<?php namespace App\Database\Models;

use App\Database\Types\DateTimeType;
use Doctrine\DBAL\Types\Type;
use Limoncello\JsonApi\Models\RelationshipTypes;

/**
 * @package App
 */
class User extends Model
{
    /** @inheritdoc */
    const TABLE_NAME = 'users';

    /** @inheritdoc */
    const FIELD_ID = 'id_user';

    /** Relationship name */
    const REL_ROLE = 'role';

    /** Relationship name */
    const REL_POSTS = 'posts';

    /** Relationship name */
    const REL_COMMENTS = 'comments';

    /** Field name */
    const FIELD_ID_ROLE = Role::FIELD_ID;

    /** Field name */
    const FIELD_TITLE = 'title';

    /** Field name */
    const FIELD_FIRST_NAME = 'first_name';

    /** Field name */
    const FIELD_LAST_NAME = 'last_name';

    /** Field name */
    const FIELD_EMAIL = 'email';

    /** Field name */
    const FIELD_PASSWORD_HASH = 'password_hash';

    /** Field name */
    const FIELD_LANGUAGE = 'language';

    /** Field name */
    const FIELD_API_TOKEN = 'api_token';

    /** Field limit */
    const MIN_FIELD_PASSWORD = 6;

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes()
    {
        return [
            self::FIELD_ID            => Type::INTEGER,
            self::FIELD_ID_ROLE       => Type::INTEGER,
            self::FIELD_TITLE         => Type::STRING,
            self::FIELD_FIRST_NAME    => Type::STRING,
            self::FIELD_LAST_NAME     => Type::STRING,
            self::FIELD_EMAIL         => Type::STRING,
            self::FIELD_PASSWORD_HASH => Type::STRING,
            self::FIELD_LANGUAGE      => Type::STRING,
            self::FIELD_API_TOKEN     => Type::STRING,
            self::FIELD_CREATED_AT    => DateTimeType::NAME,
            self::FIELD_UPDATED_AT    => DateTimeType::NAME,
            self::FIELD_DELETED_AT    => DateTimeType::NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths()
    {
        return [
            self::FIELD_TITLE         => 255,
            self::FIELD_FIRST_NAME    => 255,
            self::FIELD_LAST_NAME     => 255,
            self::FIELD_EMAIL         => 255,
            self::FIELD_PASSWORD_HASH => 255,
            self::FIELD_LANGUAGE      => 255,
            self::FIELD_API_TOKEN     => 255,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships()
    {
        return [
            RelationshipTypes::BELONGS_TO => [
                self::REL_ROLE => [Role::class, self::FIELD_ID_ROLE, Role::REL_USERS],
            ],
            RelationshipTypes::HAS_MANY   => [
                self::REL_POSTS    => [Post::class, Post::FIELD_ID_BOARD, Post::REL_USER],
                self::REL_COMMENTS => [Comment::class, Comment::FIELD_ID_USER, Comment::REL_USER],
            ],
        ];
    }
}
