<?php namespace App\Api;

use Config\ConfigInterface;
use Config\Services\JsonApi\JsonApiConfigInterface as C;
use Interop\Container\ContainerInterface;
use Limoncello\JsonApi\Adapters\FilterOperations;
use Limoncello\JsonApi\Adapters\PaginationStrategy;
use Limoncello\JsonApi\Builders\QueryBuilder;
use Limoncello\JsonApi\Contracts\FactoryInterface;
use Limoncello\JsonApi\Crud;
use Limoncello\Models\Contracts\SchemaStorageInterface;
use PDO;

/**
 * @package App
 */
abstract class BaseApi extends Crud
{
    /** Model class the API work with (must be overridden in child classes) */
    const MODEL = null;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        /** @var FactoryInterface $factory */
        $factory = $container->get(FactoryInterface::class);

        /** @var SchemaStorageInterface $modelSchemes */
        $modelSchemes = $container->get(SchemaStorageInterface::class);

        /** @var PDO $pdo */
        $pdo = $container->get(PDO::class);

        $translator       = $factory->createTranslator();
        $queryBuilder     = new QueryBuilder($translator);
        $filterOperations = new FilterOperations();

        /** @var ConfigInterface $config */
        $config        = $container->get(ConfigInterface::class);
        $jsonApiConfig = $config->getConfig()[ConfigInterface::KEY_JSON_API];
        $encodeConfig  = $jsonApiConfig[C::KEY_JSON];

        $paging = array_key_exists(C::KEY_JSON_RELATIONSHIP_PAGING_SIZE, $encodeConfig) === true ?
            new PaginationStrategy($encodeConfig[C::KEY_JSON_RELATIONSHIP_PAGING_SIZE]) : new PaginationStrategy();

        $repository = $factory->createRepository(
            static::MODEL,
            $pdo,
            $modelSchemes,
            $queryBuilder,
            $filterOperations,
            $paging,
            $translator
        );

        parent::__construct($repository, $factory);
    }
}
