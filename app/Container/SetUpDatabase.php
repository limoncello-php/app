<?php namespace App\Container;

use App\Commands\CacheModelSchemes;
use Config\ConfigInterface as C;
use Config\Services\Database\DatabaseConfigInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use Limoncello\JsonApi\Contracts\Models\ModelSchemesInterface;
use Limoncello\JsonApi\Models\ModelSchemes;

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
            $connection = DriverManager::getConnection($dbConfig[DatabaseConfigInterface::CONNECTION_CONFIG]);

            return $connection;
        };

        $container[ModelSchemesInterface::class] = function (ContainerInterface $container) {
            /** @var C $config */
            $config        = $container->get(C::class);
            $modelSchemes  = new ModelSchemes();
            $cachedRoutes  = '\\' . CacheModelSchemes::CACHED_NAMESPACE . '\\' .
                CacheModelSchemes::CACHED_CLASS . '::' . CacheModelSchemes::CACHED_METHOD;
            if ($config->useAppCache() === true && is_callable($cachedRoutes) === true) {
                $schemesData = call_user_func($cachedRoutes);
                $modelSchemes->setData($schemesData);
            } else {
                $config       = $container->get(C::class);
                $dbConfig     = $config->getConfig()[C::KEY_DB];
                $modelClasses = $dbConfig[DatabaseConfigInterface::MODELS_LIST];
                CacheModelSchemes::buildModelSchemes($modelSchemes, $modelClasses);
            }

            return $modelSchemes;
        };
    }
}
