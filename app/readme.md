`Data` folder contains application database models, database migrations and seedings.

`Http` folder contains HTTP routing and controllers.

`Json` folder contains your [JSON API](http://jsonapi.org/) implementation code including `Validators` for input data, mapping JSON data to Models with `Schemes` and `API` level. 

`Authorization` folder contains rules for authorization user actions.

`Commands` folder contains your application commands which could be executed from [composer](https://getcomposer.org/). You can create very well commented boilerplate with
 
 ```bash
 composer l:commands create <Name of New Command Class>
 ```

`Container` folder contains configurators for application [Container (PSR 11)](http://www.php-fig.org/psr/). When the application starts it creates a container and configures it with configurators from `Container` folder, providers used in the application and configurators set up for HTTP routes.
