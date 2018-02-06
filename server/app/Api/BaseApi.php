<?php namespace App\Api;

use App\Data\Models\CommonFields;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Limoncello\Contracts\Authentication\AccountManagerInterface;
use Limoncello\Contracts\Authorization\AuthorizationManagerInterface;
use Limoncello\Contracts\Exceptions\AuthorizationExceptionInterface;
use Limoncello\Flute\Adapters\ModelQueryBuilder;
use Limoncello\Flute\Api\Crud;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Limoncello\Flute\Types\JsonApiDateTimeType;
use Limoncello\Passport\Contracts\Authentication\PassportAccountInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 *
 * Here you can put common CRUD code.
 */
abstract class BaseApi extends Crud
{
    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    final public function readRelationship(
        $index,
        string $name,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): PaginatedDataInterface {
        assert(false, 'Use specialized reading methods instead.');
        throw new InvalidArgumentException();
    }

    /**
     * @param               $index
     * @param string        $name
     * @param iterable|null $relationshipFilters
     * @param iterable|null $relationshipSorts
     *
     * @return PaginatedDataInterface
     */
    protected function readRelationshipInt(
        $index,
        string $name,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): PaginatedDataInterface {
        return parent::readRelationship($index, $name, $relationshipFilters, $relationshipSorts);
    }

    /**
     * Authorize action for current user.
     *
     * @param string          $action
     * @param string|null     $resourceType
     * @param string|int|null $resourceIdentity
     *
     * @return void
     *
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function authorize(string $action, string $resourceType = null, $resourceIdentity = null)
    {
        /** @var AuthorizationManagerInterface $manager */
        $manager = $this->getContainer()->get(AuthorizationManagerInterface::class);
        $manager->authorize($action, $resourceType, $resourceIdentity);
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveResourceOnCreate(ModelQueryBuilder $builder): ModelQueryBuilder
    {
        return $this->addCreatedAt(parent::builderSaveResourceOnCreate($builder));
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveResourceOnUpdate(ModelQueryBuilder $builder): ModelQueryBuilder
    {
        return $this->addUpdatedAt(parent::builderSaveResourceOnUpdate($builder));
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveRelationshipOnCreate($relationshipName, ModelQueryBuilder $builder): ModelQueryBuilder
    {
        return $this->addCreatedAt(parent::builderSaveRelationshipOnCreate($relationshipName, $builder));
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveRelationshipOnUpdate($relationshipName, ModelQueryBuilder $builder): ModelQueryBuilder
    {
        return $this->addCreatedAt(parent::builderSaveRelationshipOnUpdate($relationshipName, $builder));
    }

    /**
     * @param ModelQueryBuilder $builder
     *
     * @return ModelQueryBuilder
     *
     * @throws DBALException
     */
    protected function addCreatedAt(ModelQueryBuilder $builder): ModelQueryBuilder
    {
        // `Doctrine` specifics: `setValue` works for inserts and `set` for updates
        $timestamp = $this->convertDateTimeToDbValue($builder, new DateTimeImmutable());
        $builder->setValue(CommonFields::FIELD_CREATED_AT, $builder->createNamedParameter($timestamp));

        return $builder;
    }

    /**
     * @param ModelQueryBuilder $builder
     *
     * @return ModelQueryBuilder
     *
     * @throws DBALException
     */
    protected function addUpdatedAt(ModelQueryBuilder $builder): ModelQueryBuilder
    {
        // `Doctrine` specifics: `setValue` works for inserts and `set` for updates
        $timestamp = $this->convertDateTimeToDbValue($builder, new DateTimeImmutable());
        $builder->set(CommonFields::FIELD_UPDATED_AT, $builder->createNamedParameter($timestamp));

        return $builder;
    }

    /**
     * @param QueryBuilder      $builder
     * @param DateTimeInterface $dateTime
     *
     * @return string
     *
     * @throws DBALException
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function convertDateTimeToDbValue(QueryBuilder $builder, DateTimeInterface $dateTime): string
    {
        $type  = Type::getType(JsonApiDateTimeType::NAME);
        $value = $type->convertToDatabaseValue($dateTime, $builder->getConnection()->getDatabasePlatform());

        return $value;
    }

    /**
     * The method assumes an account is logged in and therefore has less checks.
     *
     * @return int|string|null
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getCurrentUserIdentity()
    {
        /** @var AccountManagerInterface $manager */
        /** @var PassportAccountInterface $account */
        $manager = $this->getContainer()->get(AccountManagerInterface::class);
        $account = $manager->getAccount();
        $userId  = $account->getUserIdentity();

        return $userId;
    }

    /**
     * @param iterable $first
     * @param iterable $second
     *
     * @return iterable
     */
    protected function addIterable(iterable $first, iterable $second): iterable
    {
        foreach ($first as $key => $value) {
            yield $key => $value;
        }
        foreach ($second as $key => $value) {
            yield $key => $value;
        }
    }
}
