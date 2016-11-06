<?php namespace Config;

use App\Contracts\Config\Database as C;
use App\Database\Models\Model;
use Limoncello\Core\Config\ArrayConfig;
use ReflectionClass;

/**
 * @package Config
 */
class Database extends ArrayConfig
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct([C::class => [
            C::CONNECTION_PARAMS => [
                'driver'       => getenv('DB_DRIVER'),
                'host'         => getenv('DB_HOST'),
                'port'         => getenv('DB_PORT'),
                'dbname'       => getenv('DB_DATABASE'),
                'user'         => getenv('DB_USER_NAME'),
                'password'     => getenv('DB_PASSWORD'),
                'charset'      => getenv('DB_CHARSET'),

                // you can set driver specific options here...
                'driverOptions'=> [
                    // ... such as time zone for MySQL
                    // \PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = 'SYSTEM'",
                ],
            ],
            C::MODELS_LIST => $this->getModelClassesList(),
        ]]);
    }

    /**
     * @return string[]
     */
    private function getModelClassesList()
    {
        // result will be cached for production so performance is not an issue here

        foreach (glob(Model::MODELS_FOLDER . DIRECTORY_SEPARATOR . '*.php') as $filePath) {
            /** @noinspection PhpIncludeInspection */
            require_once  $filePath;
        }

        $modelClasses = [];
        foreach (get_declared_classes() as $class) {
            $reflection = new ReflectionClass($class);
            if ($reflection->inNamespace() === true &&
                $reflection->getNamespaceName() === Model::MODELS_NAMESPACE &&
                $reflection->isAbstract() === false &&
                $reflection->isSubclassOf(Model::class) === true
            ) {
                $modelClasses[] = $class;
            }
        }

        return $modelClasses;
    }
}
