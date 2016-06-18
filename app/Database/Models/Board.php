<?php namespace App\Database\Models;

use Limoncello\Models\FieldTypes;
use Limoncello\Models\RelationshipTypes;

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
            self::FIELD_ID         => FieldTypes::INT,
            self::FIELD_TITLE      => FieldTypes::STRING,
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
