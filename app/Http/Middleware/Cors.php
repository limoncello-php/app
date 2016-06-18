<?php namespace App\Http\Middleware;

use Closure;
use Interop\Container\ContainerInterface;
use Neomerx\Cors\Contracts\AnalysisResultInterface;
use Neomerx\Cors\Contracts\AnalyzerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

/**
 * @package App
 */
class Cors
{
    /**
     * @param ServerRequestInterface $request
     * @param Closure                $next
     * @param ContainerInterface     $container
     *
     * @return ResponseInterface
     */
    public static function handle(ServerRequestInterface $request, Closure $next, ContainerInterface $container)
    {
        /** @var AnalyzerInterface $analyzer */
        $analyzer = $container->get(AnalyzerInterface::class);
        $cors     = $analyzer->analyze($request);

        switch ($cors->getRequestType()) {
            case AnalysisResultInterface::TYPE_REQUEST_OUT_OF_CORS_SCOPE:
                // call next middleware handler
                return $next($request);

            case AnalysisResultInterface::TYPE_ACTUAL_REQUEST:
                // actual CORS request
                /** @var ResponseInterface $response */
                $response    = $next($request);
                $corsHeaders = $cors->getResponseHeaders();

                // add CORS headers to Response $response
                foreach ($corsHeaders as $name => $value) {
                    $response = $response->withHeader($name, $value);
                }

                return $response;

            case AnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST:
                $corsHeaders = $cors->getResponseHeaders();

                // return 200 HTTP with $corsHeaders
                return new EmptyResponse(200, $corsHeaders);

            case AnalysisResultInterface::ERR_NO_HOST_HEADER:
            case AnalysisResultInterface::ERR_ORIGIN_NOT_ALLOWED:
            case AnalysisResultInterface::ERR_METHOD_NOT_SUPPORTED:
            case AnalysisResultInterface::ERR_HEADERS_NOT_SUPPORTED:
            default:
                // return 4XX HTTP error
                return new EmptyResponse(400);
        }
    }
}
