`Api` folder contains native PHP implementation for application API such as CRUD (Create, Read, Update and Delete) operations.

`Authentication` folders contains application authentication implementations. By default it comes with cookie-based authentication for traditional HTML web pages and header-based for JSON API.

`Authorization` folder contains rules for authorizing authenticated user for making API calls such as view, create, update, delete resources and etc.

`Commands` folder contains your application console commands which could be executed from [composer](https://getcomposer.org/). A well commented command boilerplate could be generated with
 ```bash
 composer l:commands create <Name of New Command Class>
 ```

`Container` folder contains configurators for application [Container (PSR 11)](http://www.php-fig.org/psr/). If you need external libraries accessible from the application container (e.g. mailer, payments, etc) you can add it here.

`Data` folder contains application database models, database migrations and seedings.

`Json` folder contains your [JSON API](http://jsonapi.org/) implementation code such as controllers, mapping between JSON data and Models with `Schemas`, middleware, and etc.

`Routes` folder contains routing for web and API.

`Validation` folder contains validation rules and validators for HTTP forms, HTTP query parameters and JSON API inputs.  

`Web` folder contains Web implementation code such as controllers, middleware, and etc.
