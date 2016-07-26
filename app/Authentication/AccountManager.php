<?php namespace App\Authentication;

use App\Authentication\Contracts\AccountManagerInterface;
use App\Database\Models\User;

/**
 * @package App
 */
class AccountManager extends \Limoncello\Auth\Authentication\AccountManager implements AccountManagerInterface
{
    /**
     * @inheritdoc
     */
    public function setUser(User $user = null)
    {
        $this->setAccount(new Account($user));

        return $this;
    }
}
