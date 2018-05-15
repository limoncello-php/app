<?php namespace App\Api;

use App\Authorization\RoleRules;
use App\Data\Models\Role as Model;
use App\Json\Schemas\RoleSchema as Schema;
use Limoncello\Contracts\Exceptions\AuthorizationExceptionInterface;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
class RolesApi extends BaseApi
{
    /**
     * @param ContainerInterface $container
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container, Model::class);
    }

    /**
     * @inheritdoc
     *
     * @throws AuthorizationExceptionInterface
     */
    public function create($index, iterable $attributes, iterable $toMany): string
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Schema::TYPE, $index);

        return parent::create($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     *
     * @throws AuthorizationExceptionInterface
     */
    public function update($index, iterable $attributes, iterable $toMany): int
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Schema::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     *
     * @throws AuthorizationExceptionInterface
     */
    public function remove($index): bool
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Schema::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     *
     * @throws AuthorizationExceptionInterface
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(RoleRules::ACTION_VIEW_ROLES, Schema::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     *
     * @throws AuthorizationExceptionInterface
     */
    public function read($index)
    {
        $this->authorize(RoleRules::ACTION_VIEW_ROLES, Schema::TYPE, $index);

        return parent::read($index);
    }

    /**
     * @param string|int    $index
     * @param iterable|null $relationshipFilters
     * @param iterable|null $relationshipSorts
     *
     * @return PaginatedDataInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws AuthorizationExceptionInterface
     */
    public function readUsers(
        $index,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): PaginatedDataInterface {
        $this->authorize(RoleRules::ACTION_VIEW_ROLE_USERS, Schema::TYPE, $index);

        return $this->readRelationshipInt($index, Model::REL_USERS, $relationshipFilters, $relationshipSorts);
    }
}
