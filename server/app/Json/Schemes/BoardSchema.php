<?php namespace App\Json\Schemes;

use App\Data\Models\Board as Model;

/**
 * @package App
 */
class BoardSchema extends BaseSchema
{
    /** Type */
    const TYPE = 'boards';

    /** Model class name */
    const MODEL = Model::class;

    /** Attribute name */
    const ATTR_TITLE = 'title';

    /** Attribute name */
    const ATTR_IMG_URL = 'img-url';

    /** Relationship name */
    const REL_POSTS = 'posts';

    /**
     * @inheritdoc
     */
    public static function getMappings(): array
    {
        return [
            self::SCHEMA_ATTRIBUTES => [
                self::RESOURCE_ID     => Model::FIELD_ID,
                self::ATTR_TITLE      => Model::FIELD_TITLE,
                self::ATTR_IMG_URL    => Model::FIELD_IMG_URL,
                self::ATTR_CREATED_AT => Model::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT => Model::FIELD_UPDATED_AT,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_POSTS => Model::REL_POSTS,
            ],
        ];
    }
}
