<?php namespace App\Api;

use App\Database\Models\User as Model;
use App\Schemes\UserSchema as Schema;
use Limoncello\Crypt\Contracts\HasherInterface;

/**
 * @package App
 */
class UsersApi extends BaseApi
{
    const MODEL = Model::class;

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
}
