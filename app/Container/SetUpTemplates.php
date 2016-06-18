<?php namespace App\Container;

use Config\ConfigInterface as C;
use Config\Services\Templates\Templates;
use Config\Services\Templates\TemplatesInterface as TI;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 */
trait SetUpTemplates
{
    /**
     * @param Container $container
     *
     * @return void
     */
    protected static function setUpTemplates(Container $container)
    {
        $container[TI::class] = function (ContainerInterface $container) {
            /** @var C $config */
            $config = $container->get(C::class);
            list($templatesPath, $cachePath) = $config->getConfig()[C::KEY_TEMPLATES];
            $templateEngine = new Templates($templatesPath, $cachePath);

            return $templateEngine;
        };
    }
}
