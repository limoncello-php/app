<?php namespace App\Api;

use App\Database\Models\User as Model;
use App\Database\Models\User;
use App\Schemes\UserSchema as Schema;
use Doctrine\DBAL\Connection;
use Limoncello\Crypt\Contracts\HasherInterface;

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

        parent::update($index, $attributes, $toMany);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return null|array
     */
    public function authenticate($email, $password)
    {
        $container = $this->getContainer();
        /** @var Connection $connection */
        $connection = $container->get(Connection::class);
        $sqlQuery   = 'SELECT ' . Model::FIELD_ID . ', ' . Model::FIELD_PASSWORD_HASH .
            ' FROM ' . Model::TABLE_NAME . ' WHERE ' . Model::FIELD_EMAIL . ' = ? LIMIT 1';
        $row = $connection->executeQuery($sqlQuery, [$email])->fetch();
        if (empty($row) === true) {
            return null;
        }

        /** @var HasherInterface $hasher */
        $hasher = $container->get(HasherInterface::class);
        if ($hasher->verify($password, $row[Model::FIELD_PASSWORD_HASH]) === false) {
            return null;
        }

        $token  = bin2hex(random_bytes(16));
        $userId = $row[Model::FIELD_ID];
        $result = $connection->update(
            User::TABLE_NAME,
            [Model::FIELD_API_TOKEN => $token],
            [Model::FIELD_ID => $userId]
        );

        return $result > 0 ? [
            self::KEY_USER_ID => $userId,
            self::KEY_SECRET  => $token,
        ] : null;
    }
}
