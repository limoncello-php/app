<?php namespace App\Http\Validators;

use App\Database\Models\Board;
use App\Database\Models\Post as Model;
use App\Schemes\PostSchema as Schema;
use Limoncello\Validation\Contracts\RuleInterface;

/**
 * @package App
 */
class PostsValidator extends BaseValidator
{
    /**
     * @param array $json
     *
     * @return array
     */
    public function parseAndValidateOnCreate(array $json)
    {
        $schema         = $this->getJsonSchemes()->getSchemaByType(Schema::MODEL);
        $idRule         = $this->absentOrNull();
        $attributeRules = [
            Schema::ATTR_TITLE => $this->required($this->title()),
            Schema::ATTR_TEXT  => $this->required($this->text()),
        ];
        $toOneRules     = [
            Schema::REL_BOARD => $this->required($this->boardId()),
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
    public function parseAndValidateOnUpdate($index, array $json)
    {
        $schema         = $this->getJsonSchemes()->getSchemaByType(Schema::MODEL);
        $idRule         = $this->idEquals($index);
        $attributeRules = [
            Schema::ATTR_TITLE => $this->title(),
            Schema::ATTR_TEXT  => $this->text(),
        ];

        // do not allow changing boards for existing posts
        $toOneRules = [
        ];

        list (, $attrCaptures, $toManyCaptures) =
            $this->assert($schema, $json, $idRule, $attributeRules, $toOneRules);

        return [$attrCaptures, $toManyCaptures];
    }

    /**
     * @return RuleInterface
     */
    private function boardId()
    {
        return $this->dbId(Board::TABLE_NAME, Board::FIELD_ID);
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
    private function text()
    {
        return $this->textValue();
    }
}
