The overall process is

- HTTP request containing form data is parsed and the data are validated with `Validators`.
- The validated input data are sent to API.

`Controllers` folder contains HTTP controllers.

`Validators` folder contains validation rules for input JSON API data.

Controller methods should have the following signature

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
