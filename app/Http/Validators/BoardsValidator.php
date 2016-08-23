<?php namespace App\Http\Validators;

use App\Database\Models\Board as Model;
use App\Schemes\BoardSchema as Schema;
use Limoncello\Validation\Contracts\RuleInterface;

/**
 * @package App
 */
class BoardsValidator extends BaseValidator
{
    /**
     * @param array $json
     *
     * @return array
     */
    public function parseAndValidateOnCreate(array $json)
    {
        $schema = $this->getJsonSchemes()->getSchemaByType(Schema::MODEL);
        $idRule = $this->absentOrNull();
        $attributeRules = [
            Schema::ATTR_TITLE => $this->required($this->name()),
        ];

        list (, $attrCaptures, $toManyCaptures) = $this->assert($schema, $json, $idRule, $attributeRules);

        return [$attrCaptures, $toManyCaptures];
    }

    /**
     * @param string|int $index
     * @param array      $json
     *
     * @return array
     */
    public function parseAndValidateOnUpdate($index, array $json)
    {
        $schema = $this->getJsonSchemes()->getSchemaByType(Schema::MODEL);
        $idRule = $this->idEquals($index);
        $attributeRules = [
            Schema::ATTR_TITLE => $this->required($this->name()),
        ];

        list (, $attrCaptures, $toManyCaptures) = $this->assert($schema, $json, $idRule, $attributeRules);

        return [$attrCaptures, $toManyCaptures];
    }

    /**
     * @return RuleInterface
     */
    private function name()
    {
        return $this->andX(
            $this->textValue(Model::getAttributeLengths()[Model::FIELD_TITLE]),
            $this->isUnique(Model::TABLE_NAME, Model::FIELD_TITLE)
        );
    }
}
