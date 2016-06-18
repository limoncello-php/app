<?php namespace App\Database\Models;

use Limoncello\Models\FieldTypes;
use Limoncello\Models\RelationshipTypes;

/**
 * @package App
 */
class Comment extends Model
{
    /** @inheritdoc */
    const TABLE_NAME = 'comments';

    /** @inheritdoc */
    const FIELD_ID = 'id_comment';

    /** Field name */
    const FIELD_ID_POST = Post::FIELD_ID;

    /** Field name */
    const FIELD_ID_USER = User::FIELD_ID;

    /** Relationship name */
    const REL_POST = 'post';

    /** Relationship name */
    const REL_USER = 'user';

    /** Field name */
    const FIELD_TEXT = 'text';

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes()
    {
        return [
            self::FIELD_ID         => FieldTypes::INT,
            self::FIELD_ID_POST    => FieldTypes::INT,
            self::FIELD_ID_USER    => FieldTypes::INT,
            self::FIELD_TEXT       => FieldTypes::TEXT,
            self::FIELD_CREATED_AT => FieldTypes::DATE,
            self::FIELD_UPDATED_AT => FieldTypes::DATE,
            self::FIELD_DELETED_AT => FieldTypes::DATE,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships()
    {
        return [
            RelationshipTypes::BELONGS_TO => [
                self::REL_POST => [Post::class, self::FIELD_ID_POST, Post::REL_COMMENTS],
                self::REL_USER => [User::class, self::FIELD_ID_USER, User::REL_COMMENTS],
            ],
        ];
    }
}
