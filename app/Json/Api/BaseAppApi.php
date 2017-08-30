<?php namespace App\Json\Api;

use App\Data\Models\CommonFields;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Authentication\AccountManagerInterface;
use Limoncello\Contracts\Authorization\AuthorizationManagerInterface;
use Limoncello\Flute\Api\Crud;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Limoncello\Flute\Http\Query\FilterParameterCollection;
use Limoncello\Flute\Types\DateTimeJsonApiStringType;
use Limoncello\Passport\Contracts\Authentication\PassportAccountInterface;

/**
 * @package App
 *
 * Here you can put common CRUD code.
 */
abstract class BaseAppApi extends Crud
{
    /**
     * Should return authorization action name and resource type for reading a relationship.
     *
     * @param int|string                     $index
     * @param string                         $name
     * @param FilterParameterCollection|null $filterParams
     * @param array|null                     $sortParams
     * @param array|null                     $pagingParams
     *
     * @return array [string $action, string|null $resourceType]
     */
    abstract protected function getAuthorizationActionAndResourceTypeForRelationship(
        $index,
        string $name,
        FilterParameterCollection $filterParams = null,
        array $sortParams = null,
        array $pagingParams = null
    ): array;

    /**
     * Authorizes reading relationship.
     *
     * @param int|string                     $index
     * @param string                         $name
     * @param FilterParameterCollection|null $filterParams
     * @param array|null                     $sortParams
     * @param array|null                     $pagingParams
     *
     * @return PaginatedDataInterface
     */
    public function readRelationship(
        $index,
        string $name,
        FilterParameterCollection $filterParams = null,
        array $sortParams = null,
        array $pagingParams = null
    ): PaginatedDataInterface {
        list ($action, $resourceType) = static::getAuthorizationActionAndResourceTypeForRelationship(
            $index,
            $name,
            $filterParams,
            $sortParams,
            $pagingParams
        );
        $this->authorize($action, $resourceType, $index);

        return parent::readRelationship($index, $name, $filterParams, $sortParams, $pagingParams);
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
    protected function builderSaveResourceOnCreate(QueryBuilder $builder): QueryBuilder
    {
        return $this->addCreatedAt(parent::builderSaveResourceOnCreate($builder));
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveResourceOnUpdate(QueryBuilder $builder): QueryBuilder
    {
        return $this->addUpdatedAt(parent::builderSaveResourceOnUpdate($builder));
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveRelationshipOnCreate($relationshipName, QueryBuilder $builder): QueryBuilder
    {
        return $this->addCreatedAt(parent::builderSaveRelationshipOnCreate($relationshipName, $builder));
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveRelationshipOnUpdate($relationshipName, QueryBuilder $builder): QueryBuilder
    {
        return $this->addCreatedAt(parent::builderSaveRelationshipOnUpdate($relationshipName, $builder));
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return QueryBuilder
     */
    protected function addCreatedAt(QueryBuilder $builder)
    {
        // `Doctrine` specifics: `setValue` works for inserts and `set` for updates
        $timestamp = $this->convertDateTimeToDbValue($builder, new DateTimeImmutable());
        $builder->setValue(CommonFields::FIELD_CREATED_AT, $builder->createNamedParameter($timestamp));

        return $builder;
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return QueryBuilder
     */
    protected function addUpdatedAt(QueryBuilder $builder): QueryBuilder
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
        if (Type::hasType(DateTimeJsonApiStringType::NAME) === false) {
            Type::addType(DateTimeJsonApiStringType::NAME, DateTimeJsonApiStringType::class);
        }

        $type  = Type::getType(DateTimeJsonApiStringType::NAME);
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
