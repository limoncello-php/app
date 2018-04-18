[![License](https://img.shields.io/packagist/l/limoncello-php/app.svg)](https://packagist.org/packages/limoncello-php/app)

<a href="https://github.com/limoncello-php/framework" target="_blank"><img src="https://github.com/limoncello-php/app/blob/develop/public/img/limoncello.png" alt="Limoncello PHP" title="Limoncello PHP" align="right" width="200" height="200" /></a>

### Summary

Limoncello App is a fully featured OAuth 2.0 [JSON API](http://jsonapi.org/) quick start application.

Out-of-the-box it has

- [JSON API](http://jsonapi.org/) CRUD operations (create, read, update and delete) for a few sample resources with `to-one`, `to-many` and `many-to-many` relationship types.
- Support for such JSON API [features](http://jsonapi.org/format/#fetching) as resource inclusion, sparse field sets, sorting, filtering and pagination.
- Database migrations and seedings.
- OAuth 2.0 server authentication and role authorization.
- Admin panel for managing users and roles.
- Cross-Origin Resource Sharing (CORS).
- JSON API errors.
- API tests.
- Web tests.

Supported features
- Multiple nested paths resource inclusion (e.g. `posts,posts.user,posts.comments.user`).
- Filtering and sorting by multiple attributes in resources and its relationships.
- Supported operators `=`, `eq`, `equals`, `!=`, `neq`, `not-equals`, `<`, `lt`, `less-than`, `<=`, `lte`, `less-or-equals`, `>`, `gt`, `greater-than`, `>=`, `gte`, `greater-or-equals`, `like`, `not-like`, `in`, `not-in`, `is-null`, `not-null`.
- Pagination works for main resources and resources in relationships. Limits for maximum number of resources are configurable.

Based on
- [Zend Diactoros](https://github.com/zendframework/zend-diactoros)
- [Doctrine](http://www.doctrine-project.org/)
- [Pimple](http://pimple.sensiolabs.org/)
- [Monolog](https://github.com/Seldaek/monolog)
- [FastRoute](https://github.com/nikic/FastRoute)
- [Twig](https://twig.sensiolabs.org/)
- [JSON API implementation](https://github.com/neomerx/json-api)
- [Cross-Origin Resource Sharing](https://github.com/neomerx/cors-psr7)
- Built with :heart: [Limoncello](https://github.com/limoncello-php/framework)

It could be a great start if you are planning to develop JSON API.

Feel free to ask questions and thank you for supporting the project with :star:.

### Installation

#### 1 Create project

```bash
$ composer create-project --prefer-dist limoncello-php/app app_name
$ cd app_name
```

Recommended additional step
```bash
$ npm install
```
or
```bash
$ yarn install
```

#### 2 Run server

Application runs PHP built-in server on port 8080

```bash
$ composer serve
```

Recommended
```bash
$ npm serve
```
or
```bash
$ yarn serve
```

> Port could be configured in `composer.json` or `client/webpack/base.config.js`

The easiest way to play with the server is `Postman`.

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/e7ba014d506f62918b0f#?env%5Blocal%20HTTP%20(8080)%5D=W3siZW5hYmxlZCI6dHJ1ZSwia2V5Ijoic2VydmVyIiwidmFsdWUiOiJsb2NhbGhvc3Q6ODA4MCIsInR5cGUiOiJ0ZXh0In0seyJlbmFibGVkIjp0cnVlLCJrZXkiOiJ0b2tlbiIsInZhbHVlIjoiMjlhNDU0OGZkZWY1NDFiNWZiYjA3ODhjYzM2YzBiM2U1OTNlODY5ODk5YjA1IiwidHlwZSI6InRleHQifV0=)

API documentation and code snippets [here](https://documenter.getpostman.com/view/53867/limoncello-app/6Z3usWQ).

![Requests in Postman](server/resources/img/screen-shot.png)


#### 3 Turn on production mode (optional)

**By default** the application is installed in **development mode** (less performance, tests and development libraries are available). Application could be switched into **production mode** (higher performance, no tests, no development libraries) with command

```bash
$ composer build
```

### Testing

```bash
$ composer test
```

### License

[MIT license](http://opensource.org/licenses/MIT)
