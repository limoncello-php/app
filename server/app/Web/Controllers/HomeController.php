<?php namespace App\Web\Controllers;

use App\Web\Views;
use Limoncello\Flute\Contracts\Http\Controller\ControllerIndexInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class HomeController extends BaseController implements ControllerIndexInterface
{
    /** Route name for home page */
    const ROUTE_NAME_HOME = 'home_index';

    /**
     * @inheritdoc
     */
    public static function index(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        // render HTML body for response
        $body = static::view($container, Views::HOME_PAGE, [
            'title' => 'Limoncello',
        ]);

        return new HtmlResponse($body);
    }
}
