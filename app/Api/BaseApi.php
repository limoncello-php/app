<?php namespace App\Api;

use App\Authentication\Contracts\AccountManagerInterface;
use App\Database\Models\Model;
use App\Database\Models\User;
use App\Database\Types\DateTimeType;
use DateTime;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Interop\Container\ContainerInterface;
use Limoncello\JsonApi\Api\Crud;
use Limoncello\JsonApi\Contracts\Adapters\PaginationStrategyInterface;
use Limoncello\JsonApi\Contracts\Adapters\RepositoryInterface;
use Limoncello\JsonApi\Contracts\FactoryInterface;
use Limoncello\JsonApi\Contracts\Models\ModelSchemesInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @param string       $columnName
     * @param string       $paramName
     *
     * @return QueryBuilder
     */
    protected function addCurrentUserCondition(QueryBuilder $builder, $columnName, $paramName = ':curUserId')
    {
        /** @var AccountManagerInterface $accountManager */
        $accountManager = $this->getContainer()->get(AccountManagerInterface::class);

        // user must be signed-in
        $currentUser = $accountManager->getAccount()->getUser();
        $userIndex   = $currentUser->{User::FIELD_ID};

        $tableName = $this->getModelSchemes()->getTable($this->getModelClass());
        $fullColumn = $this->buildTableColumn($tableName, $columnName);

        $builder->andWhere($fullColumn . " = $paramName")->setParameter($paramName, $userIndex);

        return $builder;
    }

    /**
     * @param QueryBuilder $builder
     * @param DateTime     $dateTime
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function convertDateTimeToDbValue(QueryBuilder $builder, DateTime $dateTime)
    {
        $type  = Type::getType(DateTimeType::NAME);
        $value = $type->convertToDatabaseValue($dateTime, $builder->getConnection()->getDatabasePlatform());

        return $value;
    }

    /**
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    protected function buildTableColumn($table, $column)
    {
        return "`$table`.`$column`";
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return QueryBuilder
     */
    private function addCreatedAt(QueryBuilder $builder)
    {
        // `Doctrine` specifics: `setValue` works for inserts and `set` for updates
        $timestamp = $this->convertDateTimeToDbValue($builder, new DateTime());
        $builder->setValue(Model::FIELD_CREATED_AT, $builder->createNamedParameter($timestamp));

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
        $timestamp = $this->convertDateTimeToDbValue($builder, new DateTime());
        $builder->set(Model::FIELD_UPDATED_AT, $builder->createNamedParameter($timestamp));

        return $builder;
    }
}
