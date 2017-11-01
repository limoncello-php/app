<?php namespace App\Api;

use App\Data\Models\CommonFields;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Authentication\AccountManagerInterface;
use Limoncello\Contracts\Authorization\AuthorizationManagerInterface;
use Limoncello\Flute\Adapters\ModelQueryBuilder;
use Limoncello\Flute\Api\Crud;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Limoncello\Flute\Types\JsonApiDateTimeType;
use Limoncello\Passport\Contracts\Authentication\PassportAccountInterface;

/**
 * @package App
 *
 * Here you can put common CRUD code.
 */
abstract class BaseApi extends Crud
{
    /**
     * Should return authorization action name and resource type for reading a relationship.
     *
     * @param string        $name
     * @param iterable|null $relationshipFilters
     * @param iterable|null $relationshipSorts
     *
     * @return array [string $action, string|null $resourceType]
     */
    abstract protected function getAuthorizationActionAndResourceTypeForRelationship(
        string $name,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): array;

    /**
     * @inheritdoc
     */
    public function readRelationship(
        $index,
        string $name,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): PaginatedDataInterface {
        list ($action, $resourceType) = static::getAuthorizationActionAndResourceTypeForRelationship(
            $name,
            $relationshipFilters,
            $relationshipSorts
        );
        $this->authorize($action, $resourceType, $index);

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
}
