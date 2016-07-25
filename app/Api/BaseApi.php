<?php namespace App\Api;

use App\Database\Models\Model;
use Doctrine\DBAL\Query\QueryBuilder;
use Interop\Container\ContainerInterface;
use Limoncello\JsonApi\Api\Crud;
use Limoncello\JsonApi\Contracts\Adapters\PaginationStrategyInterface;
use Limoncello\JsonApi\Contracts\Adapters\RepositoryInterface;
use Limoncello\JsonApi\Contracts\FactoryInterface;
use Limoncello\Models\Contracts\ModelSchemesInterface;

/**
 * @package App
 */
abstract class BaseApi extends Crud
{
    /** Model class the API work with (must be overridden in child classes) */
    const MODEL = null;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @inheritdoc
     */
    public function __construct(
        FactoryInterface $factory,
        RepositoryInterface $repository,
        ModelSchemesInterface $modelSchemes,
        PaginationStrategyInterface $paginationStrategy,
        ContainerInterface $container
    ) {
        parent::__construct(
            $factory,
            static::MODEL,
            $repository,
            $modelSchemes,
            $paginationStrategy
        );
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveResourceOnCreate(QueryBuilder $builder)
    {
        return $this->addCreatedAt(parent::builderSaveResourceOnCreate($builder));
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveResourceOnUpdate(QueryBuilder $builder)
    {
        return $this->addUpdatedAt(parent::builderSaveResourceOnUpdate($builder));
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveRelationshipOnCreate($relationshipName, QueryBuilder $builder)
    {
        return $this->addCreatedAt(parent::builderSaveRelationshipOnCreate($relationshipName, $builder));
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveRelationshipOnUpdate($relationshipName, QueryBuilder $builder)
    {
        return $this->addCreatedAt(parent::builderSaveRelationshipOnUpdate($relationshipName, $builder));
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return QueryBuilder
     */
    private function addCreatedAt(QueryBuilder $builder)
    {
        // `Doctrine` specifics: `setValue` works for inserts and `set` for updates
        $builder->setValue(Model::FIELD_CREATED_AT, $builder->createNamedParameter(date('Y-m-d H:i:s')));

        return $builder;
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return QueryBuilder
     */
    private function addUpdatedAt(QueryBuilder $builder)
    {
        // `Doctrine` specifics: `setValue` works for inserts and `set` for updates
        $builder->set(Model::FIELD_UPDATED_AT, $builder->createNamedParameter(date('Y-m-d H:i:s')));

        return $builder;
    }
}
