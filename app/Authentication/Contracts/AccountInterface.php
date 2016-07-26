<?php namespace App\Authentication\Contracts;

use App\Database\Models\User;

/**
 * @package App
 */
interface AccountInterface extends \Limoncello\Auth\Contracts\Authentication\AccountInterface
{
    /** Property key */
    const PROP_USER = 0;

    /** Property key */
    const PROP_IS_SIGNED_IN = 0;

    /**
     * @return bool
     */
    public function isSignedIn();

    /**
     * @return bool
     */
    public function isAnonymous();

    /**
     * @return User|null
     */
    public function getUser();
}
