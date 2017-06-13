Authorization rules are described with classes which should implement interface `AuthorizationRulesInterface`. This interface is just a 'marker' so no specific methods are required.

In order to add an `authorization action` to your application you will add a method with the name of the action as shown below.

```php
<?php namespace App\Authorization;

use Limoncello\Application\Authorization\AuthorizationRulesTrait;
use Limoncello\Application\Contracts\Authorization\AuthorizationRulesInterface;
use Limoncello\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;

class CustomRules implements AuthorizationRulesInterface
{
    use AuthorizationRulesTrait;

    public static function nameOfAction(ContextInterface $context): bool
    {
        return ...;
    }

    // other action methods
    ...
}
```

The example above creates an action in global scope. As name of the action should be unique in the application the method name should be unique across all global authorization actions in your application.

Actions could be grouped into scopes (e.g. 'users', 'documents' and etc). In that case their names should be unique only within their scopes. In order to create scoped action an interface `ResourceAuthorizationRulesInterface` should be implemented.

`ContextInterface` will have all the information from application you might need such as requested resource (type, id or both) current user, `ServerRequestInterface` and application `ContainerInterface` with methods

- `ctxGetAction(ContextInterface $context)`
- `ctxGetResourceType(ContextInterface $context)`
- `ctxGetResourceIdentity(ContextInterface $context)`
- `ctxGetCurrentAccount(ContextInterface $context)`
- `ctxGetContainer(ContextInterface $context)`
