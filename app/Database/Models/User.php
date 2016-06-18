<?php namespace App\Database\Models;

use Limoncello\Models\FieldTypes;
use Limoncello\Models\RelationshipTypes;

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

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes()
    {
        return [
            self::FIELD_ID            => FieldTypes::INT,
            self::FIELD_ID_ROLE       => FieldTypes::INT,
            self::FIELD_TITLE         => FieldTypes::STRING,
            self::FIELD_FIRST_NAME    => FieldTypes::STRING,
            self::FIELD_LAST_NAME     => FieldTypes::STRING,
            self::FIELD_EMAIL         => FieldTypes::STRING,
            self::FIELD_PASSWORD_HASH => FieldTypes::STRING,
            self::FIELD_LANGUAGE      => FieldTypes::STRING,
            self::FIELD_API_TOKEN     => FieldTypes::STRING,
            self::FIELD_CREATED_AT    => FieldTypes::DATE,
            self::FIELD_UPDATED_AT    => FieldTypes::DATE,
            self::FIELD_DELETED_AT    => FieldTypes::DATE,
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
