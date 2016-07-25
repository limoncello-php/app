<?php namespace App\Http\Controllers;

use App\Container\SetUpConfig;
use App\Container\SetUpDatabase;
use App\Container\SetUpJsonApi;
use App\Container\SetUpLogs;
use Limoncello\ContainerLight\Container;

/**
 * @package App
 */
abstract class BaseController extends \Limoncello\JsonApi\Http\BaseController
{
    use SetUpConfig, SetUpJsonApi, SetUpLogs, SetUpDatabase;

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
        self::setUpDatabase($container);
        self::setUpJsonApi($container);
    }
}
