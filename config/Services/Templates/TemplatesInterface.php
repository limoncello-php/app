<?php namespace Config\Services\Templates;

/**
 * @package Config
 */
interface TemplatesInterface extends \Limoncello\Templates\Contracts\TemplatesInterface
{
    // Template list
    //
    // Templates by default are located at `resources/views` folder.
    // More templates are added with constant name (typically file name) and
    // this name should be added to `\Config\Services\Templates\Templates::getTemplatesList`.
    //
    // Twig namespaces are supported in names (for more see http://twig.sensiolabs.org/doc/api.html).
    /** Template name */
    const TPL_WELCOME = 'welcome.html.twig';


    // Settings

    /** Twig templates location folder */
    const TEMPLATES_FOLDER = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
        '..' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';

    /** Twig cache location folder */
    const CACHE_FOLDER = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
        '..' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'templates';
}
