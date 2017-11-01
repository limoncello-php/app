<?php namespace App\Api;

use App\Authorization\RoleRules;
use App\Data\Models\Role as Model;
use App\Json\Schemes\RoleScheme as Scheme;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class RolesApi extends BaseApi
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
    public function create($index, iterable $attributes, iterable $toMany): string
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Scheme::TYPE, $index);

        return parent::create($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, iterable $attributes, iterable $toMany): int
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Scheme::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function remove($index): bool
    {
        $this->authorize(RoleRules::ACTION_ADMIN_ROLES, Scheme::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(RoleRules::ACTION_VIEW_ROLES, Scheme::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     */
    public function read($index)
    {
        $this->authorize(RoleRules::ACTION_VIEW_ROLES, Scheme::TYPE, $index);

        return parent::read($index);
    }

    /**
     * @inheritdoc
     */
    protected function getAuthorizationActionAndResourceTypeForRelationship(
        string $name,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): array {
        // if you add new relationships available for reading
        // don't forget to tell the authorization subsystem what are the corresponding auth actions.

        //if ($name === Model::REL_1) {
        //    $pair = [ModelAuthRules::ACTION_VIEW_REL_1, Scheme::TYPE];
        //} else {
        //    assert($name === Model::REL_2);
        //    $pair = [ModelAuthRules::ACTION_VIEW_REL_2, Scheme::TYPE];
        //}
        //return $pair;

        assert(false, "Authorization action is not configured for reading `$name` relationship.");

        return [];
    }
}
