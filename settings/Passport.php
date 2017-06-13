<?php namespace Settings;

use App\Data\Models\User;
use App\Json\Api\UsersApi;
use Doctrine\DBAL\Connection;
use Limoncello\Crypt\Contracts\HasherInterface;
use Limoncello\Flute\Contracts\FactoryInterface;
use Limoncello\Passport\Package\PassportSettings;
use PDO;
use Psr\Container\ContainerInterface;

/**
 * @package Settings
 */
class Passport extends PassportSettings
{
    /** URI to handle OAuth scope approval for code and implicit grants. */
    const APPROVAL_URI = 'oauth-scope-approval';

    /** URI to handle OAuth critical errors such as invalid client ID or unsupported grant types. */
    const ERROR_URI = 'oauth-scope-approval';

    /** Default OAuth client ID */
    const DEFAULT_CLIENT_ID = 'default_client';

    /** Config key */
    const KEY_DEFAULT_CLIENT_NAME = self::KEY_LAST + 1;

    /** Config key */
    const KEY_DEFAULT_CLIENT_REDIRECT_URIS = self::KEY_DEFAULT_CLIENT_NAME + 1;

    /** Scope ID */
    const SCOPE_ADMIN_USERS = 'manage_users';

    /** Scope ID */
    const SCOPE_ADMIN_BOARDS = 'manage_boards';

    /** Scope ID */
    const SCOPE_ADMIN_MESSAGES = 'manage_messages';

    /** Scope ID */
    const SCOPE_ADMIN_ROLES = 'manage_roles';

    /** Scope ID */
    const SCOPE_VIEW_ROLES = 'view_roles';

    /** Scope ID */
    const SCOPE_ADMIN_OAUTH = 'manage_oauth';

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        $settings = parent::get();

        $settings[static::KEY_DEFAULT_CLIENT_NAME] = getenv('APP_NAME');

        return $settings;
    }

    /**
     * @inheritdoc
     */
    protected function getApprovalUri(): string
    {
        return self::APPROVAL_URI;
    }

    /**
     * @inheritdoc
     */
    protected function getErrorUri(): string
    {
        return self::ERROR_URI;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultClientId(): string
    {
        return self::DEFAULT_CLIENT_ID;
    }

    /**
     * @inheritdoc
     */
    protected function getUserTableName(): string
    {
        return User::TABLE_NAME;
    }

    /**
     * @inheritdoc
     */
    protected function getUserPrimaryKeyName(): string
    {
        return User::FIELD_ID;
    }

    /**
     * @inheritdoc
     */
    protected function getUserCredentialsValidator(): callable
    {
        return [static::class, 'validateUser'];
    }

    /**
     * @inheritdoc
     */
    protected function getUserScopeValidator(): callable
    {
        return [static::class, 'validateScope'];
    }

    /**
     * @param ContainerInterface $container
     * @param string             $userName
     * @param string             $password
     *
     * @return int|null
     */
    public static function validateUser(ContainerInterface $container, string $userName, string $password)
    {
        /** @var Connection $connection */
        $connection = $container->get(Connection::class);

        $query = $connection->createQueryBuilder();
        $query
            ->select([User::FIELD_ID, User::FIELD_EMAIL, User::FIELD_PASSWORD_HASH])
            ->from(User::TABLE_NAME)
            ->where(User::FIELD_EMAIL . '=' . $query->createPositionalParameter($userName))
            ->setMaxResults(1);
        $user = $query->execute()->fetch(PDO::FETCH_ASSOC);
        if ($user === false) {
            return null;
        }

        /** @var HasherInterface $hasher */
        $hasher = $container->get(HasherInterface::class);
        if ($hasher->verify($password, $user[User::FIELD_PASSWORD_HASH]) === false) {
            return null;
        }

        return (int)$user[User::FIELD_ID];
    }

    /**
     * @param ContainerInterface $container
     * @param int                $userId
     * @param array|null         $scope
     *
     * @return null
     */
    public static function validateScope(ContainerInterface $container, int $userId, array $scope = null)
    {
        assert($container || $userId || $scope);

        // Here is the place you can implement your scope limitation for users. Such as
        // limiting scopes that could be assigned for the user token.
        // It could be role based system or any other system that suits your application.
        //
        // Possible return values:
        // - `null` means no scope changes for the user.
        // - `array` with scope identities. Token issued for the user will be limited to this scope.
        // - authorization exception if you want to stop token issuing process and notify the user
        //   do not have enough rights to issue requested scopes.

        $result = null;
        if ($scope !== null) {
            /** @var UsersApi $usersApi */
            /** @var FactoryInterface $factory */
            $factory  = $container->get(FactoryInterface::class);
            $usersApi = $factory->createApi(UsersApi::class);

            $userScopes    = $usersApi->readScopes($userId);
            $adjustedScope = array_intersect($userScopes, $scope);
            if (count($adjustedScope) !== count($scope)) {
                $result = $adjustedScope;
            }
        }

        return $result;
    }
}
