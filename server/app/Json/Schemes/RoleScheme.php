<?php namespace App\Json\Schemes;

use App\Data\Models\Role as Model;

/**
 * @package App
 */
class RoleScheme extends BaseScheme
{
    /** Type */
    const TYPE = 'roles';

    /** Model class name */
    const MODEL = Model::class;

    /** Attribute name */
    const ATTR_DESCRIPTION = 'description';

    /**
     * @inheritdoc
     */
    public static function getMappings(): array
    {
        return [
            self::SCHEMA_ATTRIBUTES => [
                self::RESOURCE_ID      => Model::FIELD_ID,
                self::ATTR_DESCRIPTION => Model::FIELD_DESCRIPTION,
                self::ATTR_CREATED_AT  => Model::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT  => Model::FIELD_UPDATED_AT,
            ],
        ];
    }
}
