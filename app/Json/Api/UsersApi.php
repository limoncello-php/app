<?php namespace App\Json\Api;

use App\Authorization\UserRules;
use App\Data\Models\RoleScope;
use App\Data\Models\User as Model;
use App\Json\Schemes\UserScheme as Scheme;
use Doctrine\DBAL\Connection;
use Limoncello\Crypt\Contracts\HasherInterface;
use Limoncello\Flute\Contracts\Api\ModelsDataInterface;
use Limoncello\Flute\Http\Query\FilterParameterCollection;
use PDO;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class UsersApi extends BaseAppApi
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
        $this->authorize(UserRules::ACTION_MANAGE_USERS, Scheme::TYPE);

        // in attributes were captured validated input password we need to convert it into password hash
        $attributes = $this->replacePasswordWithHash($attributes);

        return parent::create($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, array $attributes, array $toMany = []): int
    {
        $this->authorize(UserRules::ACTION_MANAGE_USERS, Scheme::TYPE, $index);

        // in attributes might be captured validated input password we need to convert it into password hash
        if (array_key_exists(Scheme::CAPTURE_NAME_PASSWORD, $attributes) === true) {
            $attributes = $this->replacePasswordWithHash($attributes);
        }

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function delete($index): int
    {
        $this->authorize(UserRules::ACTION_MANAGE_USERS, Scheme::TYPE, $index);

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
        $this->authorize(UserRules::ACTION_VIEW_USERS, Scheme::TYPE);

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
        $this->authorize(UserRules::ACTION_VIEW_USERS, Scheme::TYPE, $index);

        return parent::read($index, $filterParams, $includePaths);
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function readScopes(int $userId): array
    {
        /** @var Connection $connection */
        $connection = $this->getContainer()->get(Connection::class);
        $query      = $connection->createQueryBuilder();
        $users = 'u';
        $uRoleId = Model::FIELD_ID_ROLE;
        $rolesScopes = 'rs';
        $rsRoleId = RoleScope::FIELD_ID_ROLE;
        $query
            ->select(RoleScope::FIELD_ID_SCOPE)
            ->from(Model::TABLE_NAME, $users)
            ->leftJoin($users, RoleScope::TABLE_NAME, $rolesScopes, "$users.$uRoleId = $rolesScopes.$rsRoleId")
            ->where(Model::FIELD_ID . '=' . $query->createPositionalParameter($userId, PDO::PARAM_INT));
        $statement = $query->execute();

        $scopes = [];
        while (($fetchedScope = $statement->fetchColumn()) !== false) {
            $scopes[] = $fetchedScope;
        }

        return $scopes;
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

        if ($name === Model::REL_POSTS) {
            $pair = [UserRules::ACTION_VIEW_USER_POSTS, Scheme::TYPE];
        } else {
            assert($name === Model::REL_COMMENTS);
            $pair = [UserRules::ACTION_VIEW_USER_COMMENTS, Scheme::TYPE];
        }

        return $pair;
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    private function replacePasswordWithHash(array $attributes)
    {
        $password = $attributes[Scheme::CAPTURE_NAME_PASSWORD];
        unset($attributes[Scheme::CAPTURE_NAME_PASSWORD]);

        /** @var HasherInterface $hasher */
        $hasher = $this->getContainer()->get(HasherInterface::class);
        $attributes[Model::FIELD_PASSWORD_HASH] = $hasher->hash($password);

        return $attributes;
    }
}
