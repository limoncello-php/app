<?php namespace Settings;

use Dotenv\Dotenv;
use Limoncello\Templates\Package\TemplatesSettings;

/**
 * @package Settings
 */
class Templates extends TemplatesSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        $appRootFolder   = implode(DIRECTORY_SEPARATOR, [__DIR__, '..']);
        $templatesFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'resources', 'views']);
        $cacheFolder     = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'storage', 'cache', 'templates']);

        return [

                static::KEY_IS_DEBUG         => filter_var(getenv('APP_IS_DEBUG'), FILTER_VALIDATE_BOOLEAN),
                static::KEY_IS_AUTO_RELOAD   => filter_var(getenv('APP_IS_DEBUG'), FILTER_VALIDATE_BOOLEAN),
                static::KEY_APP_ROOT_FOLDER  => $appRootFolder,
                static::KEY_TEMPLATES_FOLDER => $templatesFolder,
                static::KEY_CACHE_FOLDER     => $cacheFolder,

            ] + parent::getSettings();
    }
}
