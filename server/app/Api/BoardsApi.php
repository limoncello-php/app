<?php namespace App\Api;

use App\Authorization\BoardRules;
use App\Data\Models\Board as Model;
use App\Json\Schemes\BoardSchema as Schema;
use Limoncello\Contracts\Exceptions\AuthorizationExceptionInterface;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
class BoardsApi extends BaseApi
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
        $this->authorize(BoardRules::ACTION_ADMIN_BOARDS, Schema::TYPE, $index);

        return parent::create($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, iterable $attributes, iterable $toMany): int
    {
        $this->authorize(BoardRules::ACTION_ADMIN_BOARDS, Schema::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function remove($index): bool
    {
        $this->authorize(BoardRules::ACTION_ADMIN_BOARDS, Schema::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(BoardRules::ACTION_VIEW_BOARDS, Schema::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     */
    public function read($index)
    {
        $this->authorize(BoardRules::ACTION_VIEW_BOARDS, Schema::TYPE, $index);

        return parent::read($index);
    }

    /**
     * @param string|int    $index
     * @param iterable|null $relationshipFilters
     * @param iterable|null $relationshipSorts
     *
     * @return PaginatedDataInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws AuthorizationExceptionInterface
     */
    public function readPosts(
        $index,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): PaginatedDataInterface {
        $this->authorize(BoardRules::ACTION_VIEW_BOARD_POSTS, Schema::TYPE, $index);

        return $this->readRelationshipInt($index, Model::REL_POSTS, $relationshipFilters, $relationshipSorts);
    }
}
