<?php namespace App\Schemes;

use App\Database\Models\Post as Model;

/**
 * @package App
 */
class PostSchema extends BaseSchema
{
    /** Type */
    const TYPE = 'posts';

    /** Model class name */
    const MODEL = Model::class;

    /** Attribute name */
    const ATTR_TITLE = 'title';

    /** Attribute name */
    const ATTR_TEXT = 'text';

    /** Relationship name */
    const REL_USER = 'user';

    /** Relationship name */
    const REL_BOARD = 'board';

    /** Relationship name */
    const REL_COMMENTS = 'comments';

    /**
     * @inheritdoc
     */
    public static function getMappings()
    {
        return [
            self::SCHEMA_ATTRIBUTES => [
                self::ATTR_TITLE      => Model::FIELD_TITLE,
                self::ATTR_TEXT       => Model::FIELD_TEXT,
                self::ATTR_CREATED_AT => Model::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT => Model::FIELD_UPDATED_AT,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_USER     => Model::REL_USER,
                self::REL_BOARD    => Model::REL_BOARD,
                self::REL_COMMENTS => Model::REL_COMMENTS,
            ],
        ];
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    protected function getExcludesFromDefaultShowSelfLinkInRelationships()
    {
        return [
            self::REL_USER  => true,
            self::REL_BOARD => true,
        ];
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    protected function getExcludesFromDefaultShowRelatedLinkInRelationships()
    {
        return [
            self::REL_USER  => true,
            self::REL_BOARD => true,
        ];
    }
}
