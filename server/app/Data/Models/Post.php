<?php namespace App\Data\Models;

use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Contracts\Data\RelationshipTypes;
use Limoncello\Flute\Types\JsonApiDateTimeType;

/**
 * @package App
 */
class Post implements ModelInterface, CommonFields
{
    /** Table name */
    const TABLE_NAME = 'posts';

    /** Primary key */
    const FIELD_ID = 'id_post';

    /** Field name */
    const FIELD_ID_BOARD = Board::FIELD_ID;

    /** Field name */
    const FIELD_ID_USER = User::FIELD_ID;

    /** Field name */
    const FIELD_TITLE = 'title';

    /** Field name */
    const FIELD_TEXT = 'text';

    /** Relationship name */
    const REL_BOARD = 'board';

    /** Relationship name */
    const REL_USER = 'user';

    /** Relationship name */
    const REL_COMMENTS = 'comments';

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
            self::FIELD_ID         => Type::INTEGER,
            self::FIELD_ID_BOARD   => Type::INTEGER,
            self::FIELD_ID_USER    => Type::INTEGER,
            self::FIELD_TITLE      => Type::STRING,
            self::FIELD_TEXT       => Type::TEXT,
            self::FIELD_CREATED_AT => JsonApiDateTimeType::NAME,
            self::FIELD_UPDATED_AT => JsonApiDateTimeType::NAME,
            self::FIELD_DELETED_AT => JsonApiDateTimeType::NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths(): array
    {
        return [
            self::FIELD_TITLE => 255,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships(): array
    {
        return [
            RelationshipTypes::BELONGS_TO => [
                self::REL_BOARD => [Board::class, self::FIELD_ID_BOARD, Board::REL_POSTS],
                self::REL_USER  => [User::class, self::FIELD_ID_USER, User::REL_POSTS],
            ],
            RelationshipTypes::HAS_MANY   => [
                self::REL_COMMENTS => [Comment::class, Comment::FIELD_ID_POST, Comment::REL_POST],
            ],
        ];
    }
}
