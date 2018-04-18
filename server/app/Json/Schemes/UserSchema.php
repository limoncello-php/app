<?php namespace App\Json\Schemes;

use App\Data\Models\User as Model;

/**
 * @package App
 */
class UserSchema extends BaseSchema
{
    /** Type */
    const TYPE = 'users';

    /** Model class name */
    const MODEL = Model::class;

    /** Attribute name */
    const ATTR_FIRST_NAME = 'first-name';

    /** Attribute name */
    const ATTR_LAST_NAME = 'last-name';

    /** Attribute name */
    const ATTR_EMAIL = 'email';

    /** Attribute name */
    const ATTR_CREATED_AT = 'created-at';

    /** Attribute name */
    const ATTR_UPDATED_AT = 'updated-at';

    /** Virtual attribute name */
    const V_ATTR_PASSWORD = 'password';

    /** Capture name */
    const CAPTURE_NAME_PASSWORD = self::V_ATTR_PASSWORD;

    /** Virtual attribute name */
    const V_ATTR_PASSWORD_CONFIRMATION = 'password-confirmation';

    /** Capture name */
    const CAPTURE_NAME_PASSWORD_CONFIRMATION = self::V_ATTR_PASSWORD_CONFIRMATION;

    /** Relationship name */
    const REL_ROLE = 'role';

    /**
     * @inheritdoc
     */
    public static function getMappings(): array
    {
        return [
            self::SCHEMA_ATTRIBUTES    => [
                self::RESOURCE_ID     => Model::FIELD_ID,
                self::ATTR_FIRST_NAME => Model::FIELD_FIRST_NAME,
                self::ATTR_LAST_NAME  => Model::FIELD_LAST_NAME,
                self::ATTR_EMAIL      => Model::FIELD_EMAIL,
                self::ATTR_CREATED_AT => Model::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT => Model::FIELD_UPDATED_AT,

                self::V_ATTR_PASSWORD              => self::CAPTURE_NAME_PASSWORD,
                self::V_ATTR_PASSWORD_CONFIRMATION => self::CAPTURE_NAME_PASSWORD,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_ROLE => Model::REL_ROLE,
            ],
        ];
    }
}
