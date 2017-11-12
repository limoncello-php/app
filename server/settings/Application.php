<?php namespace Settings;

use Dotenv\Dotenv;
use Limoncello\Contracts\Application\ApplicationConfigurationInterface;

/**
 * @package Settings
 */
class Application implements ApplicationConfigurationInterface
{
    /** Application origin HTTP scheme */
    const ORIGIN_SCHEME = 'http';

    /** Application origin HTTP host */
    const ORIGIN_HOST = 'localhost';

    /** Application origin HTTP port */
    const ORIGIN_PORT = 8080;

    /** Application origin HTTP URI */
    const ORIGIN_URI = self::ORIGIN_SCHEME . '://' . self::ORIGIN_HOST . ':' . self::ORIGIN_PORT;

    /** @var callable */
    const CACHE_CALLABLE = '\\Cached\\Application::get';

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        $routesPath     = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Routes', '*Routes.php']);
        $confPath       = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Container', '*.php']);
        $commandsFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Commands']);
        $cacheFolder    = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'storage', 'cache', 'settings']);

        return [
            static::KEY_APP_NAME                     => getenv('APP_NAME'),
            static::KEY_IS_LOG_ENABLED               => filter_var(getenv('APP_ENABLE_LOGS'), FILTER_VALIDATE_BOOLEAN),
            static::KEY_IS_DEBUG                     => filter_var(getenv('APP_IS_DEBUG'), FILTER_VALIDATE_BOOLEAN),
            static::KEY_ROUTES_PATH                  => $routesPath,
            static::KEY_CONTAINER_CONFIGURATORS_PATH => $confPath,
            static::KEY_CACHE_FOLDER                 => $cacheFolder,
            static::KEY_CACHE_CALLABLE               => static::CACHE_CALLABLE,
            static::KEY_COMMANDS_FOLDER              => $commandsFolder,
            static::KEY_PROVIDER_CLASSES             => [
                \Limoncello\Application\Packages\Application\ApplicationProvider::class,
                \Limoncello\Application\Packages\Authorization\AuthorizationProvider::class,
                //\Limoncello\Application\Packages\PDO\PdoProvider::class,
                \Limoncello\Application\Packages\Cookies\CookieProvider::class,
                \Limoncello\Application\Packages\Cors\CorsProvider::class,
                \Limoncello\Application\Packages\Data\DataProvider::class,
                \Limoncello\Application\Packages\L10n\L10nProvider::class,
                \Limoncello\Application\Packages\Monolog\MonologFileProvider::class,
                \Limoncello\Application\Packages\FileSystem\FileSystemProvider::class,
                \Limoncello\Application\Packages\FormValidation\FormValidationProvider::class,
                //\Limoncello\Application\Packages\Session\SessionProvider::class,
                \Limoncello\Crypt\Package\HasherProvider::class,
                //\Limoncello\Crypt\Package\SymmetricCryptProvider::class,
                //\Limoncello\Crypt\Package\AsymmetricPublicEncryptPrivateDecryptProvider::class,
                //\Limoncello\Crypt\Package\AsymmetricPrivateEncryptPublicDecryptProvider::class,
                //\Limoncello\Events\Package\EventProvider::class,
                \Limoncello\Flute\Package\FluteProvider::class,
                \Limoncello\Passport\Package\PassportProvider::class,
                \Limoncello\Templates\Package\TwigTemplatesProvider::class,
            ],
        ];
    }
}
