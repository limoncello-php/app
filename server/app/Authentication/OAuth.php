<?php namespace App\Authentication;

use App\Api\UsersApi;
use App\Data\Models\User;
use Doctrine\DBAL\Connection;
use Limoncello\Crypt\Contracts\HasherInterface;
use Limoncello\Flute\Contracts\FactoryInterface;
use Limoncello\Passport\Contracts\Entities\TokenInterface;
use PDO;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
final class OAuth
{
    /** @var callable */
    public const USER_VALIDATOR = [self::class, 'validateUser'];

    /** @var callable */
    public const SCOPE_VALIDATOR = [self::class, 'validateScope'];

    /** @var callable */
    public const TOKEN_CUSTOM_PROPERTIES_PROVIDER = [self::class, 'getTokenCustomProperties'];

    /**
     * @param ContainerInterface $container
     * @param string             $userName
     * @param string             $password
     *
     * @return int|null
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
     * @return null|array
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function validateScope(ContainerInterface $container, int $userId, array $scope = null): ?array
    {
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

    /**
     * @param ContainerInterface $container
     * @param TokenInterface     $token
     *
     * @return array
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getTokenCustomProperties(ContainerInterface $container, TokenInterface $token): array
    {
        $userId = $token->getUserIdentifier();

        /** @var FactoryInterface $factory */
        $factory = $container->get(FactoryInterface::class);
        /** @var UsersApi $usersApi */
        $usersApi = $factory->createApi(UsersApi::class);

        $builder = $usersApi->shouldBeUntyped()->withIndexFilter($userId)->createIndexBuilder([
            User::FIELD_EMAIL,
            User::FIELD_FIRST_NAME,
            User::FIELD_LAST_NAME,
        ]);

        $user = $usersApi->fetchRow($builder, User::class);

        return [
            User::FIELD_ID         => $userId,
            User::FIELD_EMAIL      => $user[User::FIELD_EMAIL],
            User::FIELD_FIRST_NAME => $user[User::FIELD_FIRST_NAME],
            User::FIELD_LAST_NAME  => $user[User::FIELD_LAST_NAME],
        ];
    }
}
