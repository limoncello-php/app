`Routes` maps HTTP requests (e.g. `GET /index`) with Controller methods where responses for those requests are created.

Controller methods should have the following signature
```php
class AppController
{
    public static function methodName(
        array $routeParams,
        PsrContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        return ...;
    }
}
```

where `$routeParams` would contain route parameter values if parameters were used. For example, for request `GET /users/123` if route `/users/{idx}` was it would be
```php
[
    'idx' => '123',
]
```

`$container` is the application container which could be used for getting database connections, APIs, logging and etc.

`$request` actual [HTTP Request (PSR 7)](http://www.php-fig.org/psr/psr-7/).
