<?php namespace App\Api;

use App\Database\Models\User as Model;
use App\Database\Models\User;
use App\Schemes\UserSchema as Schema;
use Limoncello\Crypt\Contracts\HasherInterface;
use Limoncello\JsonApi\Contracts\Adapters\RepositoryInterface;
use PDO;

/**
 * @package App
 */
class UsersApi extends BaseApi
{
    const MODEL = Model::class;

    /** Token key */
    const KEY_USER_ID = 'id';

    /** Token key */
    const KEY_SECRET = 'secret';

    /**
     * @inheritdoc
     */
    public function create(array $attributes, array $toMany = [])
    {
        // in attributes were captured validated input password we need to convert it into password hash

        $password = $attributes[Schema::CAPTURE_NAME_PASSWORD];
        unset($attributes[Schema::CAPTURE_NAME_PASSWORD]);

        /** @var HasherInterface $hasher */
        $hasher = $this->getContainer()->get(HasherInterface::class);
        $attributes[Model::FIELD_PASSWORD_HASH] = $hasher->hash($password);

        return parent::create($attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    public function update($index, array $attributes, array $toMany = [])
    {
        // in attributes might be captured validated input password we need to convert it into password hash
        if (array_key_exists(Schema::CAPTURE_NAME_PASSWORD, $attributes) === true) {
            $password = $attributes[Schema::CAPTURE_NAME_PASSWORD];
            unset($attributes[Schema::CAPTURE_NAME_PASSWORD]);

            /** @var HasherInterface $hasher */
            $hasher = $this->getContainer()->get(HasherInterface::class);
            $attributes[Model::FIELD_PASSWORD_HASH] = $hasher->hash($password);
        }

        return parent::update($index, $attributes, $toMany);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return null|array
     */
    public function authenticate($email, $password)
    {
        $user = $this->readUserByEmail($email);
        if ($user === null) {
            return null;
        }

        /** @var HasherInterface $hasher */
        $hasher = $this->getContainer()->get(HasherInterface::class);
        if ($hasher->verify($password, $user->{Model::FIELD_PASSWORD_HASH}) === false) {
            return null;
        }

        $token  = bin2hex(random_bytes(16));
        $userId = $user->{Model::FIELD_ID};
        $this->update($userId, [Model::FIELD_API_TOKEN => $token]);

        return [
            self::KEY_USER_ID => $userId,
            self::KEY_SECRET  => $token,
        ];
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function readUserByEmail($email)
    {
        /** @var RepositoryInterface $repository */
        $repository = $this->getContainer()->get(RepositoryInterface::class);
        $statement = $repository
            ->index(User::class)
            ->andWhere(User::FIELD_EMAIL . ' = :email')
            ->setParameter(':email', $email)
            ->setMaxResults(1)
            ->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, User::class);
        $dbResponse = $statement->fetch();

        $result = $dbResponse !== false && $dbResponse !== null ? $dbResponse : null;

        return $result;
    }
}
