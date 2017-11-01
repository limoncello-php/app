<?php namespace App\Data\Models;

use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Contracts\Data\RelationshipTypes;
use Limoncello\Flute\Types\JsonApiDateTimeType;

/**
 * @package App
 */
class Board implements ModelInterface, CommonFields
{
    /** Table name */
    const TABLE_NAME = 'boards';

    /** Primary key */
    const FIELD_ID = 'id_board';

    /** Field name */
    const FIELD_TITLE = 'title';

    /** Field name */
    const FIELD_IMG_URL = 'img_url';

    /** Relationship name */
    const REL_POSTS = 'posts';

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
            self::FIELD_TITLE      => Type::STRING,
            self::FIELD_IMG_URL    => Type::STRING,
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
            self::FIELD_TITLE   => 255,
            self::FIELD_IMG_URL => 255,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships(): array
    {
        return [
            RelationshipTypes::HAS_MANY => [
                self::REL_POSTS => [Post::class, Post::FIELD_ID_BOARD, Post::REL_BOARD],
            ],
        ];
    }
}
