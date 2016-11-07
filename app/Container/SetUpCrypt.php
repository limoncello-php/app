<?php namespace App\Container;

use Config\Crypt as C;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Config\ConfigInterface;
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
        $container[HasherInterface::class] = function (ContainerInterface $container) {
            $cryptConfig = $container->get(ConfigInterface::class)->getConfig(C::class);
            $hasher      = new Hasher($cryptConfig[C::HASH_ALGORITHM], $cryptConfig[C::HASH_COST]);

            return $hasher;
        };
    }
}
