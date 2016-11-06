<?php namespace App\Database\Models;

/**
 * @package App
 */
abstract class Model implements ModelInterface
{
    /** Table name */
    const TABLE_NAME = null;

    /** Primary key */
    const FIELD_ID = null;

    /** Field name */
    const FIELD_CREATED_AT = 'created_at';

    /** Field name */
    const FIELD_UPDATED_AT = 'updated_at';

    /** Field name */
    const FIELD_DELETED_AT = 'deleted_at';

    /** Namespace where models live */
    const MODELS_NAMESPACE = __NAMESPACE__;

    /** Folder where models live */
    const MODELS_FOLDER = __DIR__;

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
}
