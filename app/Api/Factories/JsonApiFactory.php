<?php namespace App\Api\Factories;

use App\Api\Repositories\BaseRepository;
use Doctrine\DBAL\Connection;
use Limoncello\JsonApi\Contracts\Adapters\FilterOperationsInterface;
use Limoncello\JsonApi\Contracts\I18n\TranslatorInterface;
use Limoncello\JsonApi\Contracts\Models\ModelSchemesInterface;
use Limoncello\JsonApi\Factory;

/**
 * @package App
 */
class JsonApiFactory extends Factory
{
    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    public function createRepository(
        Connection $connection,
        ModelSchemesInterface $modelSchemes,
        FilterOperationsInterface $filterOperations,
        TranslatorInterface $translator
    ) {
        return new BaseRepository(
            $connection,
            $modelSchemes,
            $filterOperations,
            $translator
        );
    }
}
