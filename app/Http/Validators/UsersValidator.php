<?php namespace App\Http\Validators;

use App\Database\Models\Role;
use App\Database\Models\User as Model;
use App\Schemes\UserSchema as Schema;
use Limoncello\Validation\Contracts\RuleInterface;

/**
 * @package App
 */
class UsersValidator extends BaseValidator
{
    /**
     * @param array $json
     *
     * @return array
     */
    public function parseAndValidateUserOnCreate(array $json)
    {
        $schema         = $this->getJsonSchemes()->getSchemaByType(Schema::MODEL);
        $idRule         = $this->absentOrNull();
        $attributeRules = [
            Schema::ATTR_TITLE      => $this->required($this->title()),
            Schema::ATTR_FIRST_NAME => $this->required($this->firstName()),
            Schema::ATTR_LAST_NAME  => $this->required($this->lastName()),
            Schema::ATTR_LANGUAGE   => $this->required($this->language()),
            Schema::ATTR_EMAIL      => $this->required($this->email()),
            Schema::V_ATTR_PASSWORD => $this->required($this->password()),
        ];
        $toOneRules     = [
            Schema::REL_ROLE => $this->required($this->roleId()),
        ];

        list (, $attrCaptures, $toManyCaptures) =
            $this->assert($schema, $json, $idRule, $attributeRules, $toOneRules);

        return [$attrCaptures, $toManyCaptures];
    }

    /**
     * @param string|int $index
     * @param array      $json
     *
     * @return array
     */
    public function parseAndValidateUserOnUpdate($index, array $json)
    {
        $schema         = $this->getJsonSchemes()->getSchemaByType(Schema::MODEL);
        $idRule         = $this->idEquals($index);
        $attributeRules = [
            Schema::ATTR_TITLE      => $this->title(),
            Schema::ATTR_FIRST_NAME => $this->firstName(),
            Schema::ATTR_LAST_NAME  => $this->lastName(),
            Schema::ATTR_LANGUAGE   => $this->language(),
            Schema::ATTR_EMAIL      => $this->email(),
            Schema::V_ATTR_PASSWORD => $this->password(),
        ];
        $toOneRules     = [
            Schema::REL_ROLE => $this->roleId(),
        ];

        list (, $attrCaptures, $toManyCaptures) =
            $this->assert($schema, $json, $idRule, $attributeRules, $toOneRules);

        return [$attrCaptures, $toManyCaptures];
    }

    /**
     * @return RuleInterface
     */
    private function roleId()
    {
        return $this->dbId(Role::TABLE_NAME, Role::FIELD_ID);
    }

    /**
     * @return RuleInterface
     */
    private function title()
    {
        return $this->textValue(Model::getAttributeLengths()[Model::FIELD_TITLE]);
    }

    /**
     * @return RuleInterface
     */
    private function firstName()
    {
        return $this->textValue(Model::getAttributeLengths()[Model::FIELD_FIRST_NAME]);
    }

    /**
     * @return RuleInterface
     */
    private function lastName()
    {
        return $this->textValue(Model::getAttributeLengths()[Model::FIELD_LAST_NAME]);
    }

    /**
     * @return RuleInterface
     */
    private function language()
    {
        return $this->textValue(Model::getAttributeLengths()[Model::FIELD_LANGUAGE]);
    }

    /**
     * @return RuleInterface
     */
    private function email()
    {
        $isEmail = static::andX(
            $this->textValue(Model::getAttributeLengths()[Model::FIELD_EMAIL]),
            $this->isEmail()
        );

        return static::andX($isEmail, $this->isUnique(Model::TABLE_NAME, Model::FIELD_EMAIL));
    }

    /**
     * @return RuleInterface
     */
    private function password()
    {
        return static::andX(static::isString(), static::stringLength(Model::MIN_FIELD_PASSWORD));
    }
}
