<?php namespace Config;

use Limoncello\Core\Config\ArrayConfig;

/**
 * @package Config
 */
class Templates extends ArrayConfig
{
    /** Config key */
    const TEMPLATES_LIST = 0;

    /** Config key */
    const TEMPLATES_FOLDER = self::TEMPLATES_LIST + 1;

    /** Config key */
    const CACHE_FOLDER = self::TEMPLATES_FOLDER + 1;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $top = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

        $isInConfigCachingProcess = getenv(Core::IN_CONFIG_CACHING) !== false;

        parent::__construct([self::class => [
            // Templates by default are located at `resources/views` folder.
            // Twig namespaces are supported in names (for more see http://twig.sensiolabs.org/doc/api.html).
            self::TEMPLATES_LIST  => [
                'welcome.html.twig',
            ],

            self::TEMPLATES_FOLDER => $top . 'resources' . DIRECTORY_SEPARATOR . 'views',
            self::CACHE_FOLDER     => $isInConfigCachingProcess === true ?
                $top . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'templates' : null,
        ]]);
    }
}
