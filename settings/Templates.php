<?php namespace Settings;

use Limoncello\Templates\Package\TemplatesSettings;

/**
 * @package Settings
 */
class Templates extends TemplatesSettings
{
    /** Settings key */
    const KEY_TEMPLATES_LIST = self::KEY_LAST + 1;

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        $templatesFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'resources', 'views']);
        $cacheFolder     = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'storage', 'cache', 'templates']);
        $templateNames   = $this->getTemplateNames($templatesFolder);

        return [
            static::KEY_TEMPLATES_FOLDER => $templatesFolder,
            static::KEY_CACHE_FOLDER     => $cacheFolder,
            static::KEY_TEMPLATES_LIST   => $templateNames,
        ];
    }
}
