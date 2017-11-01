<?php namespace Settings;

use App\Http\Routes;
use App\Json\Exceptions\ThrowableConverter;
use Limoncello\Application\Exceptions\AuthorizationException;
use Limoncello\Core\Reflection\ClassIsTrait;
use Limoncello\Flute\Package\FluteSettings;

/**
 * @package Settings
 */
class JsonApi extends FluteSettings
{
    use ClassIsTrait;

    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        $defaults = parent::getSettings();

        $schemesFolder    = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Json', 'Schemes']);
        $validatorsFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Json', 'Validators', '**']);

        return [

                static::KEY_URI_PREFIX                                => Routes::API_URI_PREFIX,
                static::KEY_THROWABLE_TO_JSON_API_EXCEPTION_CONVERTER => ThrowableConverter::class,
                static::KEY_SCHEMES_FOLDER                            => $schemesFolder,
                static::KEY_VALIDATORS_FOLDER                         => $validatorsFolder,
                static::KEY_JSON_ENCODE_OPTIONS                       => $defaults[static::KEY_JSON_ENCODE_OPTIONS] | JSON_PRETTY_PRINT,
                static::KEY_DO_NOT_LOG_EXCEPTIONS_LIST                => [

                    AuthorizationException::class,

                ] + $defaults[static::KEY_DO_NOT_LOG_EXCEPTIONS_LIST],

            ] + $defaults;
    }
}
