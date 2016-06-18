<?php namespace App\Http\Controllers;

use App\Database\Models\Model;
use App\Schemes\BaseSchema;
use Limoncello\JsonApi\Transformer\BaseDocumentTransformer;
use Neomerx\JsonApi\Exceptions\ErrorCollection;

/**
 * @package App
 */
abstract class BaseOnUpdate extends BaseDocumentTransformer
{
    /**
     * @inheritdoc
     */
    public function transformAttributes(ErrorCollection $errors, array $jsonAttributes)
    {
        unset($jsonAttributes[BaseSchema::ATTR_CREATED_AT]);
        unset($jsonAttributes[BaseSchema::ATTR_UPDATED_AT]);
        unset($jsonAttributes[BaseSchema::ATTR_DELETED_AT]);

        $modelAttributes = parent::transformAttributes($errors, $jsonAttributes);

        $modelAttributes[Model::FIELD_UPDATED_AT] = date('Y-m-d H:i:s');

        return $modelAttributes;
    }
}
