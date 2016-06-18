<?php namespace App\Schemes;

use App\Database\Models\Model;
use Limoncello\JsonApi\Schema\Schema;

/**
 * @package App
 */
abstract class BaseSchema extends Schema
{
    /** Attribute name */
    const ATTR_CREATED_AT = 'created-at';

    /** Attribute name */
    const ATTR_UPDATED_AT = 'updated-at';

    /** Attribute name */
    const ATTR_DELETED_AT = 'deleted-at';

    /**
     * @inheritdoc
     */
    public function getId($resource)
    {
        /** @var Model $modelClass */
        $modelClass = static::MODEL;
        $pkName     = $modelClass::FIELD_ID;
        $index      = $resource->{$pkName};

        return $index;
    }
}
