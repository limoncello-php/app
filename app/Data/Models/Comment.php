<?php namespace App\Data\Models;

use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Contracts\Data\RelationshipTypes;
use Limoncello\Flute\Types\JsonApiDateTimeType;

/**
 * @package App
 */
class Comment implements ModelInterface, CommonFields
{
    /** @inheritdoc */
    const TABLE_NAME = 'comments';

    /** @inheritdoc */
    const FIELD_ID = 'id_comment';

    /** Field name */
    const FIELD_ID_POST = Post::FIELD_ID;

    /** Field name */
    const FIELD_ID_USER = User::FIELD_ID;

    /** Field name */
    const FIELD_TEXT = 'text';

    /** Relationship name */
    const REL_POST = 'post';

    /** Relationship name */
    const REL_USER = 'user';

    /**
     * @inheritdoc
     */
    public static function getTableName()
    {
        return static::TABLE_NAME;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyName()
    {
        return static::FIELD_ID;
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes()
    {
        return [
            self::FIELD_ID         => Type::INTEGER,
            self::FIELD_ID_POST    => Type::INTEGER,
            self::FIELD_ID_USER    => Type::INTEGER,
            self::FIELD_TEXT       => Type::TEXT,
            self::FIELD_CREATED_AT => JsonApiDateTimeType::NAME,
            self::FIELD_UPDATED_AT => JsonApiDateTimeType::NAME,
            self::FIELD_DELETED_AT => JsonApiDateTimeType::NAME,
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
