<?php namespace Config;

use App\Contracts\Config\Templates as C;
use Limoncello\Core\Config\ArrayConfig;

/**
 * @package Config
 */
class Templates extends ArrayConfig
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $top = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

        $isInConfigCachingProcess = getenv(Core::IN_CONFIG_CACHING) !== false;

        parent::__construct([C::class => [
            // Templates by default are located at `resources/views` folder.
            // Twig namespaces are supported in names (for more see http://twig.sensiolabs.org/doc/api.html).
            C::TEMPLATES_LIST  => [
                'welcome.html.twig',
            ],

            C::TEMPLATES_FOLDER => $top . 'resources' . DIRECTORY_SEPARATOR . 'views',
            C::CACHE_FOLDER     => $isInConfigCachingProcess === true ?
                $top . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'templates' : null,
        ]]);
    }
}
