## Quick start JSON API application

Limoncello Flute is a [JSON API](http://jsonapi.org/) quick start application.
 
This application integrated with
- [JSON API implementation](https://github.com/neomerx/json-api)
- [Cross-Origin Resource Sharing](https://github.com/neomerx/cors-psr7) (CORS)

Based on
- [Zend Diactoros](https://github.com/zendframework/zend-diactoros)
- [Doctrine](http://www.doctrine-project.org/)
- [Pimple](http://pimple.sensiolabs.org/)
- [Monolog](https://github.com/Seldaek/monolog)
- [FastRoute](https://github.com/nikic/FastRoute)
- Built with :heart: [Limoncello](https://github.com/limoncello-php/app)

It could be a great start if you are planning to develop JSON API.

The application includes
- CRUD operations for a few sample resources with input data validation.
- Support for such JSON API [features](http://jsonapi.org/format/#fetching) as resource inclusion, sparse field sets, sorting, filtering and pagination.
- JSON API errors.
- API tests.

### Installation

Install [docker-compose](https://docs.docker.com/compose/)

Clone the project

Start local web server at [http://localhost:8080](http://localhost:8080) with

```
$ composer install && composer docker-up
```

> Note: use `$ composer docker-down` to stop the servers.

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/064046759f3d14d4def7#?env%5Blocal%5D=W3sia2V5Ijoic2VydmVyIiwidmFsdWUiOiJodHRwOi8vbG9jYWxob3N0Ojg4ODgvIiwidHlwZSI6InRleHQiLCJuYW1lIjoic2VydmVyIiwiZW5hYmxlZCI6dHJ1ZX0seyJrZXkiOiJ0b2tlbiIsInR5cGUiOiJ0ZXh0IiwidmFsdWUiOiJmTHZRelFKaXRuSElYUUl0MiIsImVuYWJsZWQiOnRydWV9XQ==)

![Requests in Postman](resources/img/screen-shot.png)

### License

[MIT license](http://opensource.org/licenses/MIT)
