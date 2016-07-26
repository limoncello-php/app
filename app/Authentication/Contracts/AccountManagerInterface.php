<?php namespace App\Authentication\Contracts;

use App\Database\Models\User;

/**
 * @package App
 */
interface AccountManagerInterface extends \Limoncello\Auth\Contracts\Authentication\AccountManagerInterface
{
    /**
     * @return AccountInterface|null
     */
    public function getAccount();

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user);
}
