<?php namespace App\Http\Controllers;

use Limoncello\Flute\Contracts\Http\Query\IncludeParameterInterface;
use Limoncello\Flute\Contracts\Schema\SchemaInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Psr\Container\ContainerInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class BaseController extends \Limoncello\Flute\Http\BaseController
{
    /**
     * We override the method in order to add filtering for input query parameters and `include` paths in particular.
     *
     * The default implementation do not limit include paths so in theory the user can request the entire database
     * which is obviously not desirable behaviour. The way we implement it is that we take parsed input paths and
     * leave first level once so no deep include paths.
     *
     * @param ContainerInterface          $container
     * @param EncodingParametersInterface $parameters
     * @param string                      $schemaClass
     *
     * @return array
     */
    protected static function mapQueryParameters(
        ContainerInterface $container,
        EncodingParametersInterface $parameters,
        string $schemaClass
    ): array {
        $scheme = static::SCHEMA_CLASS;
        list ($filters, $sorts, $includes, $paging) = parent::mapQueryParameters($container, $parameters, $schemaClass);

        /**
         * @var SchemaInterface           $scheme
         * @var int                       $index
         * @var IncludeParameterInterface $includeParam
         */
        if (empty($includes) === false) {
            foreach ($includes as $index => $includeParam) {
                // The Scheme has only first level relationships so it's a good way to limit the deepness.
                if ($scheme::hasRelationshipMapping($includeParam->getOriginalPath()) === false) {
                    unset($includes[$index]);
                }
            }
        }

        return [$filters, $sorts, $includes, $paging];
    }
}
