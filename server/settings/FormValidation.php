<?php namespace Settings;

use Limoncello\Application\Packages\FormValidation\FormValidationSettings;
use Limoncello\Core\Reflection\ClassIsTrait;

/**
 * @package Settings
 */
class FormValidation extends FormValidationSettings
{
    use ClassIsTrait;

    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        $defaults = parent::getSettings();

        $validatorsFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Web', 'Validators', '**']);

        return [

                static::KEY_VALIDATORS_FOLDER => $validatorsFolder,

            ] + $defaults;
    }
}
