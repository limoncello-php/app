<?php namespace App\Container;

use Config\Templates as C;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Config\ConfigInterface;
use Limoncello\Templates\Contracts\TemplatesInterface;
use Limoncello\Templates\TwigTemplates;

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
        $container[TemplatesInterface::class] = function (ContainerInterface $container) {
            $tplConfig      = $container->get(ConfigInterface::class)->getConfig(C::class);
            $templateEngine = new TwigTemplates($tplConfig[C::TEMPLATES_FOLDER], $tplConfig[C::CACHE_FOLDER]);

            return $templateEngine;
        };
    }
}
