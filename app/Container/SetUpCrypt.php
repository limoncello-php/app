<?php namespace App\Container;

use Limoncello\ContainerLight\Container;
use Limoncello\Crypt\Contracts\HasherInterface;
use Limoncello\Crypt\Hasher;

/**
 * @package App
 */
trait SetUpCrypt
{
    /**
     * @param Container $container
     *
     * @return void
     */
    protected static function setUpCrypt(Container $container)
    {
        $container[HasherInterface::class] = function () {
            return new Hasher();
        };
    }
}
