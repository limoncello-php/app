<?php namespace App\Container;

use Limoncello\ContainerLight\Container;
use Limoncello\Core\Contracts\Routing\RouterInterface;

/**
 * This config relies on the method Application::getRouter and would work only from
 * Factory trait as it's included in Application.
 *
 * @package App
 *
 * @method RouterInterface getRouter()
 */
trait AppSetUpRouter
{
    /**
     * @param Container $container
     *
     * @return void
     */
    protected function appSetUpRouter(Container $container)
    {
        $container[RouterInterface::class] = function () {
            $router = $this->getRouter();
            return $router;
        };
    }
}
