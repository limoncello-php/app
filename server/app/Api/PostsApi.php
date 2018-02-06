<?php namespace App\Api;

use App\Authorization\PostRules;
use App\Data\Models\Post as Model;
use App\Json\Schemes\PostSchema as Schema;
use Limoncello\Contracts\Exceptions\AuthorizationExceptionInterface;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
class PostsApi extends BaseApi
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
        $this->authorize(PostRules::ACTION_CREATE_POST, Schema::TYPE, $index);

        $withUserId = $this->addIterable($attributes, [Model::FIELD_ID_USER => $this->getCurrentUserIdentity()]);

        return parent::create($index, $withUserId, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, iterable $attributes, iterable $toMany): int
    {
        $this->authorize(PostRules::ACTION_EDIT_POST, Schema::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function remove($index): bool
    {
        $this->authorize(PostRules::ACTION_EDIT_POST, Schema::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(PostRules::ACTION_VIEW_POSTS, Schema::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     */
    public function read($index)
    {
        $this->authorize(PostRules::ACTION_VIEW_POSTS, Schema::TYPE, $index);

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
    public function readComments(
        $index,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
    ): PaginatedDataInterface {
        $this->authorize(PostRules::ACTION_VIEW_POST_COMMENTS, Schema::TYPE, $index);

        return $this->readRelationshipInt($index, Model::REL_COMMENTS, $relationshipFilters, $relationshipSorts);
    }
}
