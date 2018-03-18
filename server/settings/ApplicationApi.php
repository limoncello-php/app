<?php namespace Settings;

use App\Json\Exceptions\ThrowableConverter;
use App\Routes\ApiRoutes;
use Limoncello\Application\Exceptions\AuthorizationException;
use Limoncello\Core\Reflection\ClassIsTrait;
use Limoncello\Flute\Package\FluteSettings;

/**
 * @package Settings
 */
class ApplicationApi extends FluteSettings
{
    use ClassIsTrait;

    /** @inheritdoc */
    public const DEFAULT_PAGE_SIZE = 10;

    /** @inheritdoc */
    public const DEFAULT_MAX_PAGE_SIZE = 30;

    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        $defaults = parent::getSettings();

        $apiFolder      = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Api']);
        $schemesFolder  = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Json', 'Schemes']);
        $jsonCtrlFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Json', 'Controllers']);
        $valRulesFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Validation', '**']);
        $jsonValFolder  = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Validation', '**']);
        $formValFolder  = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Validation', '**']);
        $queryValFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Validation', '**']);

        return [

                static::KEY_URI_PREFIX                                => ApiRoutes::API_URI_PREFIX,
                static::KEY_THROWABLE_TO_JSON_API_EXCEPTION_CONVERTER => ThrowableConverter::class,
                static::KEY_API_FOLDER                                => $apiFolder,
                static::KEY_JSON_CONTROLLERS_FOLDER                   => $jsonCtrlFolder,
                static::KEY_SCHEMAS_FOLDER                            => $schemesFolder,
                static::KEY_JSON_VALIDATION_RULES_FOLDER              => $valRulesFolder,
                static::KEY_JSON_VALIDATORS_FOLDER                    => $jsonValFolder,
                static::KEY_FORM_VALIDATORS_FOLDER                    => $formValFolder,
                static::KEY_QUERY_VALIDATORS_FOLDER                   => $queryValFolder,
                static::KEY_JSON_ENCODE_OPTIONS                       => $defaults[static::KEY_JSON_ENCODE_OPTIONS] | JSON_PRETTY_PRINT,
                static::KEY_DO_NOT_LOG_EXCEPTIONS_LIST                => [

                    AuthorizationException::class,

                ] + $defaults[static::KEY_DO_NOT_LOG_EXCEPTIONS_LIST],

            ] + $defaults;
    }
}
