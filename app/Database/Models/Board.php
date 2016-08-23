<?php namespace App\Database\Models;

use App\Database\Types\DateTimeType;
use Doctrine\DBAL\Types\Type;
use Limoncello\JsonApi\Models\RelationshipTypes;

/**
 * @package App
 */
class Board extends Model
{
    /** @inheritdoc */
    const TABLE_NAME = 'boards';

    /** @inheritdoc */
    const FIELD_ID = 'id_board';

    /** Relationship name */
    const REL_POSTS = 'posts';

    /** Field name */
    const FIELD_TITLE = 'title';

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes()
    {
        return [
            self::FIELD_ID         => Type::INTEGER,
            self::FIELD_TITLE      => Type::STRING,
            self::FIELD_CREATED_AT => DateTimeType::NAME,
            self::FIELD_UPDATED_AT => DateTimeType::NAME,
            self::FIELD_DELETED_AT => DateTimeType::NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths()
    {
        return [
            self::FIELD_TITLE => 255,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships()
    {
        return [
            RelationshipTypes::HAS_MANY => [
                self::REL_POSTS => [Post::class, Post::FIELD_ID_BOARD, Post::REL_BOARD],
            ],
        ];
    }
}
