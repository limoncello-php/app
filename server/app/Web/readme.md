The overall process is

- HTTP request containing form data is parsed and validated.
- The validated data are sent to API.

`Controllers` folder contains web HTTP controllers.
`Middleware` folder contains middleware used with web HTTP controllers.

Controller methods should have signature

```php
class AppController
{
    public static function methodName(
        array $routeParams,
        ContainerInterface $container,
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

`$container` is the application [container (PSR 11)](http://www.php-fig.org/psr/psr-11/) which could be used for getting database connections, APIs, logging and etc.

`$request` actual [HTTP Request (PSR 7)](http://www.php-fig.org/psr/psr-7/).
