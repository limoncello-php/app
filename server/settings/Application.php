<?php namespace Settings;

use Dotenv\Dotenv;
use Limoncello\Contracts\Application\ApplicationConfigurationInterface;

/**
 * @package Settings
 */
class Application implements ApplicationConfigurationInterface
{
    /** @var callable */
    const CACHE_CALLABLE = '\\Cached\\Application::get';

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        $routesMask     = '*Routes.php';
        $routesFolder   = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Routes']);
        $webCtrlFolder  = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Web', 'Controllers']);
        $routesPath     = implode(DIRECTORY_SEPARATOR, [$routesFolder, $routesMask]);
        $confPath       = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Container', '*.php']);
        $commandsFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Commands']);
        $cacheFolder    = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'storage', 'cache', 'settings']);

        $originScheme = getenv('APP_ORIGIN_SCHEME');
        $originHost   = getenv('APP_ORIGIN_HOST');
        $originPort   = getenv('APP_ORIGIN_PORT');
        $originUri    = filter_var("$originScheme://$originHost:$originPort", FILTER_VALIDATE_URL);

        return [
            static::KEY_APP_NAME                     => getenv('APP_NAME'),
            static::KEY_IS_LOG_ENABLED               => filter_var(getenv('APP_ENABLE_LOGS'), FILTER_VALIDATE_BOOLEAN),
            static::KEY_IS_DEBUG                     => filter_var(getenv('APP_IS_DEBUG'), FILTER_VALIDATE_BOOLEAN),
            static::KEY_ROUTES_FILE_MASK             => $routesMask,
            static::KEY_ROUTES_FOLDER                => $routesFolder,
            static::KEY_WEB_CONTROLLERS_FOLDER       => $webCtrlFolder,
            static::KEY_ROUTES_PATH                  => $routesPath,
            static::KEY_CONTAINER_CONFIGURATORS_PATH => $confPath,
            static::KEY_CACHE_FOLDER                 => $cacheFolder,
            static::KEY_CACHE_CALLABLE               => static::CACHE_CALLABLE,
            static::KEY_COMMANDS_FOLDER              => $commandsFolder,
            static::KEY_APP_ORIGIN_SCHEME            => $originScheme,
            static::KEY_APP_ORIGIN_HOST              => $originHost,
            static::KEY_APP_ORIGIN_PORT              => $originPort,
            static::KEY_APP_ORIGIN_URI               => $originUri,
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
