<?php namespace App\Api;

use App\Authorization\UserRules;
use App\Data\Models\RoleScope;
use App\Data\Models\User as Model;
use App\Json\Schemes\UserScheme as Scheme;
use Doctrine\DBAL\Connection;
use Limoncello\Crypt\Contracts\HasherInterface;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use PDO;
use Psr\Container\ContainerInterface;

/**
 * @package App
 */
class UsersApi extends BaseApi
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
        $this->authorize(UserRules::ACTION_MANAGE_USERS, Scheme::TYPE);

        return parent::create($index, $this->getReplacePasswordWithHash($attributes), $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, iterable $attributes, iterable $toMany): int
    {
        $this->authorize(UserRules::ACTION_MANAGE_USERS, Scheme::TYPE, $index);

        return parent::update($index, $this->getReplacePasswordWithHash($attributes), $toMany);
    }

    /**
     * @inheritdoc
     */
    public function remove($index): bool
    {
        $this->authorize(UserRules::ACTION_MANAGE_USERS, Scheme::TYPE, $index);

        return parent::remove($index);
    }

    /**
     * @inheritdoc
     */
    public function index(): PaginatedDataInterface
    {
        $this->authorize(UserRules::ACTION_VIEW_USERS, Scheme::TYPE);

        return parent::index();
    }

    /**
     * @inheritdoc
     */
    public function read($index)
    {
        $this->authorize(UserRules::ACTION_VIEW_USERS, Scheme::TYPE, $index);

        return parent::read($index);
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    public function readScopes(int $userId): array
    {
        /** @var Connection $connection */
        $connection  = $this->getContainer()->get(Connection::class);
        $query       = $connection->createQueryBuilder();
        $users       = 'u';
        $uRoleId     = Model::FIELD_ID_ROLE;
        $rolesScopes = 'rs';
        $rsRoleId    = RoleScope::FIELD_ID_ROLE;
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
        string $name,
        iterable $relationshipFilters = null,
        iterable $relationshipSorts = null
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
     * @param iterable $attributes
     *
     * @return iterable
     */
    private function getReplacePasswordWithHash(iterable $attributes) : iterable
    {
        // in attributes were captured validated input password we need to convert it into password hash
        foreach ($attributes as $name => $value) {
            if ($name === Scheme::CAPTURE_NAME_PASSWORD) {
                /** @var HasherInterface $hasher */
                $hasher = $this->getContainer()->get(HasherInterface::class);
                $value  = $hasher->hash($value);
                $name   = Model::FIELD_PASSWORD_HASH;
            }

            yield $name => $value;
        }
    }
}
