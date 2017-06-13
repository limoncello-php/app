When the application starts it creates [Container (PSR 11)](http://www.php-fig.org/psr/) and configures it before it is accessible from middlewares and HTTP Controller.

Just create a configurator class as shown below and place it to this folder. 

```php
<?php namespace App\Container;

use Limoncello\Contracts\Application\ContainerConfiguratorInterface;
use Limoncello\Contracts\Container\ContainerInterface as LimoncelloContainerInterface;
use Limoncello\Contracts\Settings\SettingsProviderInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Settings\SomeSettings;

class CustomConfigurator implements ContainerConfiguratorInterface
{
    public static function configureContainer(LimoncelloContainerInterface $container)
    {
        $container[SomeInterface::class] = function (PsrContainerInterface $container) {
            $settings = $container->get(SettingsProviderInterface::class)->get(SomeSettings::class);
            $data     = $settings[SomeSettings::KEY_SOME_VALUE];
            
            // use settings $data to create and configure a resource
            $resource = ...;

            return $resource;
        };
        
        // you can add to $container as many items as you need
    }
}
```
