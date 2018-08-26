<?php namespace App\Json\Schemas;

use Limoncello\Common\Reflection\ClassIsTrait;
use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Flute\Schema\Schema;

/**
 * @package App
 */
abstract class BaseSchema extends Schema
{
    use ClassIsTrait;

    /** Attribute name */
    const ATTR_CREATED_AT = 'created-at';

    /** Attribute name */
    const ATTR_UPDATED_AT = 'updated-at';

    /** Attribute name */
    const ATTR_DELETED_AT = 'deleted-at';

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getId($resource): ?string
    {
        assert(get_class($resource) === static::MODEL);
        assert($this->classImplements(static::MODEL, ModelInterface::class) === true);

        /** @var ModelInterface $modelClass */
        $modelClass = static::MODEL;

        $pkName = $modelClass::getPrimaryKeyName();

        return $resource->{$pkName};
    }
}
