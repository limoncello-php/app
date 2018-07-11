<?php namespace App\Data\Models;

use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Flute\Types\DateTimeType;
use Limoncello\Passport\Entities\Scope;

/**
 * @package App
 */
class RoleScope implements ModelInterface, CommonFields
{
    /** Table name */
    const TABLE_NAME = 'roles_scopes';

    /** Primary key */
    const FIELD_ID = 'id_role_scope';

    /** Field name */
    const FIELD_ID_ROLE = Role::FIELD_ID;

    /** Field name */
    const FIELD_ID_SCOPE = Scope::FIELD_ID;

    /**
     * @inheritdoc
     */
    public static function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyName(): string
    {
        return static::FIELD_ID;
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes(): array
    {
        return [
            self::FIELD_ID         => Type::INTEGER,
            self::FIELD_ID_ROLE    => Role::getAttributeTypes()[Role::FIELD_ID],
            self::FIELD_ID_SCOPE   => Type::STRING,
            self::FIELD_CREATED_AT => DateTimeType::NAME,
            self::FIELD_UPDATED_AT => DateTimeType::NAME,
            self::FIELD_DELETED_AT => DateTimeType::NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths(): array
    {
        return [
            self::FIELD_ID_ROLE  => Role::getAttributeLengths()[Role::FIELD_ID],
            self::FIELD_ID_SCOPE => 255,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships(): array
    {
        return [];
    }
}
