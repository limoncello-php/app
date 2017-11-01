<?php namespace App\Api;

use App\Authorization\BoardRules;
use App\Data\Models\Board as Model;
use App\Json\Schemes\BoardScheme as Scheme;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class BoardsApi extends BaseApi
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
        $this->authorize(BoardRules::ACTION_ADMIN_BOARDS, Scheme::TYPE, $index);

        return parent::create($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, iterable $attributes, iterable $toMany): int
    {
        $this->authorize(BoardRules::ACTION_ADMIN_BOARDS, Scheme::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function remove($index): bool
    {
        $this->authorize(BoardRules::ACTION_ADMIN_BOARDS, Scheme::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(BoardRules::ACTION_VIEW_BOARDS, Scheme::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     */
    public function read($index)
    {
        $this->authorize(BoardRules::ACTION_VIEW_BOARDS, Scheme::TYPE, $index);

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

        assert($name === Model::REL_POSTS);
        $pair = [BoardRules::ACTION_VIEW_BOARD_POSTS, Scheme::TYPE];

        return $pair;
    }
}
