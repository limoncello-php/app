<?php namespace App\Api;

use App\Authorization\RoleRules;
use App\Data\Models\Role as Model;
use App\Json\Schemes\RoleSchema as Schema;
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
     */
    public function create($index, iterable $attributes, iterable $toMany): string
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Schema::TYPE, $index);

        return parent::create($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, iterable $attributes, iterable $toMany): int
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Schema::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function remove($index): bool
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Schema::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(RoleRules::ACTION_VIEW_ROLES, Schema::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     */
    public function read($index)
    {
        $this->authorize(RoleRules::ACTION_VIEW_ROLES, Schema::TYPE, $index);

        return parent::read($index);
    }
}
