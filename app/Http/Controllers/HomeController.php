<?php namespace App\Http\Controllers;

use Limoncello\Application\Packages\Monolog\MonologFileContainerConfigurator;
use Limoncello\Contracts\Container\ContainerInterface as LimoncelloContainerInterface;
use Limoncello\Contracts\Templates\TemplatesInterface;
use Limoncello\Templates\Package\TemplatesContainerConfigurator;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\TextResponse;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class HomeController
{
    /** @var callable */
    const INDEX_HANDLER = [self::class, 'index'];

    /** @var callable */
    const WELCOME_HANDLER = [self::class, 'welcome'];

    /** @var callable */
    const CONTAINER_EXTRA_CONFIGURATOR = [self::class, 'extraConfigurator'];

    /**
     * @return ResponseInterface
     */
    public static function index(): ResponseInterface
    {
        return new TextResponse('I\'ve got some Limoncello.');
    }

    /**
     * @param array                  $routeParams
     * @param PsrContainerInterface  $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public static function welcome(
        array $routeParams,
        PsrContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        assert($routeParams || $request);

        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);
        /** @var TemplatesInterface $templates */
        $templates = $container->get(TemplatesInterface::class);

        $body = $templates->render('welcome.html.twig', [
            'title' => 'Limoncello',
            'text'  => 'Fast and flexible PHP framework',
        ]);

        $logger->debug('\'Welcome\' template rendered');

        return new HtmlResponse($body);
    }

    /**
     * @param LimoncelloContainerInterface $container
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function extraConfigurator(LimoncelloContainerInterface $container)
    {
        TemplatesContainerConfigurator::configureContainer($container);
        MonologFileContainerConfigurator::configureContainer($container);
    }
}
