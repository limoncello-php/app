<?php namespace App\Json\Api;

use App\Authorization\CommentRules;
use App\Data\Models\Comment as Model;
use App\Json\Schemes\CommentScheme as Scheme;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class CommentsApi extends BaseApi
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
        $this->authorize(CommentRules::ACTION_CREATE_COMMENT, Scheme::TYPE, $index);

        $addCurrentUserId = function (iterable $attributes): iterable {
            foreach ($attributes as $name => $value) {
                yield $name => $value;
            }

            yield Model::FIELD_ID_USER => $this->getCurrentUserIdentity();
        };

        return parent::create($index, $addCurrentUserId($attributes), $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, iterable $attributes, iterable $toMany): int
    {
        $this->authorize(CommentRules::ACTION_EDIT_COMMENT, Scheme::TYPE, $index);

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function remove($index): bool
    {
        $this->authorize(CommentRules::ACTION_EDIT_COMMENT, Scheme::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(CommentRules::ACTION_VIEW_COMMENTS, Scheme::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     */
    public function read($index)
    {
        $this->authorize(CommentRules::ACTION_VIEW_COMMENTS, Scheme::TYPE, $index);

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
