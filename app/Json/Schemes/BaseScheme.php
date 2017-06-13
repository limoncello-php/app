<?php namespace App\Json\Schemes;

use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Core\Reflection\ClassIsTrait;
use Limoncello\Flute\Schema\Schema;

/**
 * @package App
 */
abstract class BaseScheme extends Schema
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
    public function getId($resource)
    {
        assert(get_class($resource) === static::MODEL);
        assert($this->classImplements(static::MODEL, ModelInterface::class) === true);

        /** @var ModelInterface $modelClass */
        $modelClass = static::MODEL;

        $pkName = $modelClass::getPrimaryKeyName();

        return $resource->{$pkName};
    }
}
