<?php namespace Config;

use App\Http\Controllers\BaseController;
use App\Schemes\BaseSchema;
use Limoncello\Core\Config\ArrayConfig;
use Limoncello\JsonApi\Config\JsonApiConfig;
use Limoncello\JsonApi\Contracts\Config\JsonApiConfigInterface;
use ReflectionClass;

/**
 * @package Config
 */
class JsonApi extends ArrayConfig
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $config = new JsonApiConfig();
        $config
            ->setModelSchemaMap($this->getModelToSchemaMappings())
            ->setRelationshipPagingSize(10)
            ->setJsonEncodeOptions($config->getJsonEncodeOptions() | JSON_PRETTY_PRINT)
            ->setHideVersion()
            ->setMeta([
                'name'       => 'JSON API Demo Application',
                'copyright'  => '2015-2016 info@neomerx.com',
                'powered-by' => 'Limoncello flute',
            ])
            ->setUriPrefix(BaseController::API_URI_PREFIX);

        $data = $config->getConfig();

        parent::__construct([JsonApiConfigInterface::class => $data]);
    }

    /**
     * @return string[]
     */
    private function getModelToSchemaMappings()
    {
        // result will be cached for production so performance is not an issue here

        foreach (glob(BaseSchema::SCHEMES_FOLDER . DIRECTORY_SEPARATOR . '*.php') as $filePath) {
            /** @noinspection PhpIncludeInspection */
            require_once  $filePath;
        }

        $schemaClasses = [];
        foreach (get_declared_classes() as $class) {
            $reflection = new ReflectionClass($class);
            if ($reflection->inNamespace() === true &&
                $reflection->getNamespaceName() === BaseSchema::SCHEMES_NAMESPACE &&
                $reflection->isAbstract() === false &&
                $reflection->isSubclassOf(BaseSchema::class) === true
            ) {
                /** @var BaseSchema $class */
                $schemaClasses[$class::MODEL] = $class;
            }
        }

        return $schemaClasses;
    }
}
