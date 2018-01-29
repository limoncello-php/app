`Data` folder contains application database models, database migrations and seedings.

`Routes` folder contains routing for web and API.

`Api` folder contains implementation for application API such as CRUD (Create, Read, Update and Delete) operations.

`Json` folder contains your [JSON API](http://jsonapi.org/) implementation code including controllers, mapping JSON data to Models with `Schemes` and `API` level.

`Web` folder contains web forms implementation including controllers.

`Validation` folder contains validation rules and validators for HTTP Query parameters, JSON API inputs and Web forms.  

`Authorization` folder contains rules for authorization user actions.

`Commands` folder contains your application commands which could be executed from [composer](https://getcomposer.org/). You can create very well commented boilerplate with
 
 ```bash
 composer l:commands create <Name of New Command Class>
 ```

`Container` folder contains configurators for application [Container (PSR 11)](http://www.php-fig.org/psr/). When the application starts it creates a container and configures it with configurators from `Container` folder, providers used in the application and configurators set up for HTTP routes.