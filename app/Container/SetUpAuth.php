<?php namespace App\Container;

use App\Authentication\AccountManager;
use App\Authentication\Contracts\AccountManagerInterface;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 */
trait SetUpAuth
{
    /**
     * @param Container $container
     *
     * @return void
     */
    protected static function setUpAuth(Container $container)
    {
        $container[AccountManagerInterface::class] = function () {
            return new AccountManager();
        };
    }
}
