<?php namespace Config\Services\Templates;

use Limoncello\Templates\BaseTemplates;

/**
 * @package Config
 */
class Templates extends BaseTemplates implements TemplatesInterface
{
    /**
     * @inheritdoc
     */
    public static function getTemplatesList()
    {
        return [
            self::TPL_WELCOME,
        ];
    }
}
