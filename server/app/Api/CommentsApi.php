<?php namespace App\Api;

use App\Authorization\CommentRules;
use App\Data\Models\Comment as Model;
use App\Json\Schemes\CommentSchema as Schema;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
class CommentsApi extends BaseApi
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
        $this->authorize(CommentRules::ACTION_CREATE_COMMENT, Schema::TYPE, $index);

        $withUserId = $this->addIterable($attributes, [Model::FIELD_ID_USER => $this->getCurrentUserIdentity()]);

        return parent::create($index, $withUserId, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, iterable $attributes, iterable $toMany): int
    {
        $this->authorize(CommentRules::ACTION_EDIT_COMMENT, Schema::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function remove($index): bool
    {
        $this->authorize(CommentRules::ACTION_EDIT_COMMENT, Schema::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(CommentRules::ACTION_VIEW_COMMENTS, Schema::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     */
    public function read($index)
    {
        $this->authorize(CommentRules::ACTION_VIEW_COMMENTS, Schema::TYPE, $index);

        return parent::read($index);
    }
}
