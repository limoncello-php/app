<?php namespace App\Schemes;

use App\Database\Models\Comment as Model;

/**
 * @package App
 */
class CommentSchema extends BaseSchema
{
    /** Type */
    const TYPE = 'comments';

    /** Model class name */
    const MODEL = Model::class;

    /** Attribute name */
    const ATTR_TEXT = 'text';

    /** Relationship name */
    const REL_USER = 'user';

    /** Relationship name */
    const REL_POST = 'post';

    /**
     * @inheritdoc
     */
    public static function getMappings()
    {
        return [
            self::SCHEMA_ATTRIBUTES => [
                self::ATTR_TEXT       => Model::FIELD_TEXT,
                self::ATTR_CREATED_AT => Model::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT => Model::FIELD_UPDATED_AT,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_USER     => Model::REL_USER,
                self::REL_POST     => Model::REL_POST,
            ],
        ];
    }
}
