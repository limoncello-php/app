<?php namespace App\Container;

use App\Commands\CacheModelSchemes;
use App\Database\Types\DateTimeType;
use App\Contracts\Config\Database as C;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Config\ConfigInterface;
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
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected static function setUpDatabase(Container $container)
    {
        $container[Connection::class] = function (ContainerInterface $container) {
            $dbConfig   = $container->get(ConfigInterface::class)->getConfig(C::class);
            $connection = DriverManager::getConnection($dbConfig[C::CONNECTION_PARAMS]);

            return $connection;
        };

        $container[ModelSchemesInterface::class] = function (ContainerInterface $container) {
            $modelSchemes  = new ModelSchemes();
            $cachedRoutes  = '\\' . CacheModelSchemes::CACHED_NAMESPACE . '\\' .
                CacheModelSchemes::CACHED_CLASS . '::' . CacheModelSchemes::CACHED_METHOD;
            if (is_callable($cachedRoutes) === true) {
                $schemesData = call_user_func($cachedRoutes);
                $modelSchemes->setData($schemesData);
            } else {
                $dbConfig   = $container->get(ConfigInterface::class)->getConfig(C::class);
                $modelClasses = $dbConfig[C::MODELS_LIST];
                CacheModelSchemes::buildModelSchemes($modelSchemes, $modelClasses);
            }

            return $modelSchemes;
        };

        if (Type::hasType(DateTimeType::NAME) === false) {
            Type::addType(DateTimeType::NAME, DateTimeType::class);
        }
    }
}
