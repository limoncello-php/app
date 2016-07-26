<?php namespace App\Authentication;

use App\Authentication\Contracts\AccountInterface;
use App\Database\Models\User;
use InvalidArgumentException;

/**
 * @package App
 */
class Account implements AccountInterface
{
    /**
     * @var User|null
     */
    private $user;

    /**
     * @param User|null $user
     */
    public function __construct($user = null)
    {
        $this->user = $user;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function isSignedIn()
    {
        return $this->getUser() !== null;
    }

    /**
     * @inheritdoc
     */
    public function isAnonymous()
    {
        return $this->isSignedIn() === false;
    }

    /**
     * @param int|string $key
     *
     * @return bool
     */
    public function hasProperty($key)
    {
        return
            $key === self::PROP_USER ||
            $key === self::PROP_IS_SIGNED_IN;
    }

    /**
     * @inheritdoc
     */
    public function getProperty($key)
    {
        $result = null;
        switch ($key) {
            case self::PROP_USER:
                $result = $this->getUser();
                break;
            case self::PROP_IS_SIGNED_IN:
                $result = $this->isSignedIn();
                break;
            default:
                throw new InvalidArgumentException($key);
        }

        return $result;
    }
}
