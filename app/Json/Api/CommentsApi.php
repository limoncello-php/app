<?php namespace App\Json\Api;

use App\Authorization\CommentRules;
use App\Data\Models\Comment as Model;
use App\Json\Schemes\CommentScheme as Scheme;
use Limoncello\Flute\Contracts\Api\ModelsDataInterface;
use Limoncello\Flute\Http\Query\FilterParameterCollection;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class CommentsApi extends BaseAppApi
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
        $this->authorize(CommentRules::ACTION_CREATE_COMMENT, Scheme::TYPE, $index);

        $attributes[Model::FIELD_ID_USER] = $this->getCurrentUserIdentity();

        return parent::create($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, array $attributes, array $toMany = []): int
    {
        $this->authorize(CommentRules::ACTION_EDIT_COMMENT, Scheme::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function delete($index): int
    {
        $this->authorize(CommentRules::ACTION_EDIT_COMMENT, Scheme::TYPE, $index);

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
        $this->authorize(CommentRules::ACTION_VIEW_COMMENTS, Scheme::TYPE);

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
        $this->authorize(CommentRules::ACTION_VIEW_COMMENTS, Scheme::TYPE, $index);

        return parent::read($index, $filterParams, $includePaths);
    }
}
