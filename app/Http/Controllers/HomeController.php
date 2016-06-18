<?php namespace App\Http\Controllers;

use App\Container\SetUpConfig;
use App\Container\SetUpLogs;
use App\Container\SetUpTemplates;
use Config\Services\Templates\TemplatesInterface;
use Interop\Container\ContainerInterface;
use Limoncello\ContainerLight\Container;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * @package App
 */
class HomeController
{
    use SetUpConfig, SetUpTemplates, SetUpLogs;

    /**
     * @param array              $routeParams
     * @param ContainerInterface $container
     *
     * @return ResponseInterface
     */
    public static function index(array $routeParams, ContainerInterface $container)
    {
        // suppress unused variable warning
        $routeParams ?: null;

        /** @var TemplatesInterface $templates */
        $templates = $container->get(TemplatesInterface::class);

        $body = $templates->render(TemplatesInterface::TPL_WELCOME, [
            'title' => 'Limoncello',
            'text'  => 'Fast and flexible micro-framework',
        ]);

        return new HtmlResponse($body);
    }

    /**
     * @param Container $container
     *
     * @return void
     */
    public static function welcomeConfigurator(Container $container)
    {
        self::setUpTemplates($container);
    }
}
