<?php namespace App\Http\Controllers;

use App\Container\SetUpAuth;
use App\Container\SetUpCrypt;
use App\Container\SetUpJsonApi;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 */
abstract class BaseController extends \Limoncello\JsonApi\Http\BaseController
{
    use SetUpAuth, SetUpCrypt, SetUpJsonApi;

    /** API URI prefix */
    const API_URI_PREFIX = '/api/v1';

    /** URI key used in routing table */
    const ROUTE_KEY_INDEX = 'idx';

    /**
     * @param Container $container
     *
     * @return void
     */
    public static function containerConfigurator(Container $container)
    {
        self::setUpAuth($container);
        self::setUpCrypt($container);
        self::setUpJsonApi($container);
    }
}
