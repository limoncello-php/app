`Settings` is the main way to configure the application.

In order to create custom settings a class implementing `SettingsInterface` should be created

```php
<?php namespace Settings;

use Limoncello\Contracts\Settings\SettingsInterface;

class CustomSettings implements SettingsInterface
{
    public function get(): array
    {
        return [
            'SOME_KEY' => 'some value',
        ];
    }
}
```

You can get it everywhere in the application from `Container`

```php
    $settings = $container->get(SettingsProviderInterface::class)->get(CustomSettings::class);
    $value    = $settings['SOME_KEY'];
```

You can also override settings for built-in or 3rd party components by inheriting their settings class and overriding values from `get` method. The application is smart enough to understand it should use the modified settings.
