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
}
