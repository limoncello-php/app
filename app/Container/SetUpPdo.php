<?php namespace App\Container;

use Config\ConfigInterface as C;
use Config\Services\Database\DatabaseInterface as DC;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use PDO;

/**
 * @package App
 */
trait SetUpPdo
{
    /**
     * @param Container $container
     *
     * @return void
     */
    protected static function setUpPdo(Container $container)
    {
        $container[PDO::class] = function (ContainerInterface $container) {
            /** @var C $config */
            $config   = $container->get(C::class);
            $dbConfig = $config->getConfig()[C::KEY_DB];

            $get = function ($key, $default = null) use ($dbConfig) {
                return array_key_exists($key, $dbConfig) === true ? $dbConfig[$key] : $default;
            };

            $pdo = new PDO(
                $get(DC::PDO_CONNECTION_STRING),
                $get(DC::USER_NAME),
                $get(DC::PASSWORD),
                $get(DC::PDO_OPTIONS)
            );

            return $pdo;
        };
    }
}
