<?php namespace App\Container;

use Config\ConfigInterface as C;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 */
trait SetUpDatabase
{
    /**
     * @param Container $container
     *
     * @return void
     */
    protected static function setUpDatabase(Container $container)
    {
        $container[Connection::class] = function (ContainerInterface $container) {
            /** @var C $config */
            $config     = $container->get(C::class);
            $dbConfig   = $config->getConfig()[C::KEY_DB];
            $connection = DriverManager::getConnection($dbConfig);

            return $connection;
        };
    }
}
