<?php namespace App\Json\Api;

use App\Authorization\PostRules;
use App\Data\Models\Post as Model;
use App\Json\Schemes\PostScheme as Scheme;
use Limoncello\Flute\Contracts\Api\ModelsDataInterface;
use Limoncello\Flute\Http\Query\FilterParameterCollection;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class PostsApi extends BaseAppApi
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
        $this->authorize(PostRules::ACTION_CREATE_POST, Scheme::TYPE, $index);

        $attributes[Model::FIELD_ID_USER] = $this->getCurrentUserIdentity();

        return parent::create($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, array $attributes, array $toMany = []): int
    {
        $this->authorize(PostRules::ACTION_EDIT_POST, Scheme::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function delete($index): int
    {
        $this->authorize(PostRules::ACTION_EDIT_POST, Scheme::TYPE, $index);

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
        $this->authorize(PostRules::ACTION_VIEW_POSTS, Scheme::TYPE);

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
        $this->authorize(PostRules::ACTION_VIEW_POSTS, Scheme::TYPE, $index);

        return parent::read($index, $filterParams, $includePaths);
    }

    /**
     * @inheritdoc
     */
    protected function getAuthorizationActionAndResourceTypeForRelationship(
        $index,
        string $name,
        FilterParameterCollection $filterParams = null,
        array $sortParams = null,
        array $pagingParams = null
    ): array {
        // if you add new relationships available for reading
        // don't forget to tell the authorization subsystem what are the corresponding auth actions.

        assert($name === Model::REL_COMMENTS);
        $pair = [PostRules::ACTION_VIEW_POST_COMMENTS, Scheme::TYPE];

        return $pair;
    }
}
