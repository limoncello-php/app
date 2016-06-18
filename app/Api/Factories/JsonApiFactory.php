<?php namespace App\Api\Factories;

use App\Api\Repositories\BaseRepository;
use Limoncello\JsonApi\Contracts\Adapters\FilterOperationsInterface;
use Limoncello\JsonApi\Contracts\Adapters\PaginationStrategyInterface;
use Limoncello\JsonApi\Contracts\I18n\TranslatorInterface;
use Limoncello\JsonApi\Contracts\QueryBuilderInterface;
use Limoncello\JsonApi\Factory;
use Limoncello\Models\Contracts\SchemaStorageInterface;
use PDO;

/**
 * @package App
 */
class JsonApiFactory extends Factory
{
    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    public function createRepository(
        $class,
        PDO $pdo,
        SchemaStorageInterface $schemaStorage,
        QueryBuilderInterface $builder,
        FilterOperationsInterface $filterOperations,
        PaginationStrategyInterface $relationshipPaging,
        TranslatorInterface $translator,
        $isExecuteOnByOne = true
    ) {
        return new BaseRepository(
            $this,
            $class,
            $pdo,
            $schemaStorage,
            $builder,
            $filterOperations,
            $relationshipPaging,
            $translator,
            $isExecuteOnByOne
        );
    }
}
