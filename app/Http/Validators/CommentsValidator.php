<?php namespace App\Http\Validators;

use App\Database\Models\Post;
use App\Schemes\CommentSchema as Schema;
use Limoncello\Validation\Contracts\RuleInterface;

/**
 * @package App
 */
class CommentsValidator extends BaseValidator
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
            Schema::ATTR_TEXT => $this->required($this->text()),
        ];
        $toOneRules     = [
            Schema::REL_POST => $this->required($this->postId()),
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
            Schema::ATTR_TEXT => $this->required($this->text()),
        ];

        // do not allow changing posts for existing comments
        $toOneRules = [
        ];

        list (, $attrCaptures, $toManyCaptures) =
            $this->assert($schema, $json, $idRule, $attributeRules, $toOneRules);

        return [$attrCaptures, $toManyCaptures];
    }

    /**
     * @return RuleInterface
     */
    private function postId()
    {
        return $this->dbId(Post::TABLE_NAME, Post::FIELD_ID);
    }

    /**
     * @return RuleInterface
     */
    private function text()
    {
        return $this->textValue();
    }
}
