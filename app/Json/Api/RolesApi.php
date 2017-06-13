<?php namespace App\Json\Api;

use App\Authorization\RoleRules;
use App\Data\Models\Role as Model;
use App\Json\Schemes\RoleScheme as Scheme;
use Limoncello\Flute\Contracts\Api\ModelsDataInterface;
use Limoncello\Flute\Http\Query\FilterParameterCollection;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class RolesApi extends BaseAppApi
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container, Model::class);
    }

    /**
     * @inheritdoc
     */
    public function create($index, array $attributes, array $toMany = []): string
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Scheme::TYPE, $index);

        return parent::create($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, array $attributes, array $toMany = []): int
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Scheme::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function delete($index): int
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Scheme::TYPE, $index);

        return parent::delete($index);
    }

    /**
     * @inheritdoc
     */
    public function index(
        FilterParameterCollection $filterParams = null,
        array $sortParams = null,
        array $includePaths = null,
        array $pagingParams = null
    ): ModelsDataInterface {
        $this->authorize(RoleRules::ACTION_VIEW_ROLES, Scheme::TYPE);

        return parent::index($filterParams, $sortParams, $includePaths, $pagingParams);
    }

    /**
     * @inheritdoc
     */
    public function read(
        $index,
        FilterParameterCollection $filterParams = null,
        array $includePaths = null
    ): ModelsDataInterface {
        $this->authorize(RoleRules::ACTION_VIEW_ROLES, Scheme::TYPE, $index);

        return parent::read($index, $filterParams, $includePaths);
    }
}
