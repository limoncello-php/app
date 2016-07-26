<?php namespace App\Authentication\Middleware;

use App\Api\UsersApi;
use App\Authentication\Contracts\AccountManagerInterface;
use App\Database\Models\User;
use Closure;
use Interop\Container\ContainerInterface;
use Limoncello\JsonApi\Contracts\Adapters\RepositoryInterface;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 */
class TokenAuthentication
{
    /**
     * @param ServerRequestInterface $request
     * @param Closure                $next
     * @param ContainerInterface     $container
     *
     * @return ResponseInterface
     */
    public static function handle(ServerRequestInterface $request, Closure $next, ContainerInterface $container)
    {
        $currentUser = null;
        $authHeader  = $request->getHeader('Authorization');

        // if value has Bearer token and it is a valid json with 2 required fields and they are strings
        if (empty($authHeader) === false &&
            substr($value = $authHeader[0], 0, 7) === 'Bearer ' &&
            is_string($token = substr($value, 7)) === true &&
            ($decoded = json_decode($token, true, 2)) !== false &&
            array_key_exists(UsersApi::KEY_USER_ID, $decoded) === true &&
            array_key_exists(UsersApi::KEY_SECRET, $decoded) === true &&
            is_string($userId = $decoded[UsersApi::KEY_USER_ID]) &&
            is_string($secret = $decoded[UsersApi::KEY_SECRET])
        ) {
            $currentUser = self::readUser($userId, $secret, $container);
        }

        /** @var AccountManagerInterface $accountManager */
        $accountManager = $container->get(AccountManagerInterface::class);
        $accountManager->setUser($currentUser);

        // call next middleware handler
        return $next($request);
    }

    /**
     * @param string             $userId
     * @param string             $token
     * @param ContainerInterface $container
     *
     * @return User|null
     */
    private static function readUser($userId, $token, ContainerInterface $container)
    {
        /** @var RepositoryInterface $repository */
        $repository = $container->get(RepositoryInterface::class);
        $statement = $repository
            ->read(User::class, ':id')
            ->andWhere(User::FIELD_API_TOKEN . ' = :token')
            ->setParameter(':id', $userId)
            ->setParameter(':token', $token)
            ->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, User::class);
        $dbResponse = $statement->fetch();

        $result = $dbResponse !== false && $dbResponse !== null ? $dbResponse : null;

        return $result;
    }
}
