<?php namespace Settings;

use App\Http\Routes;
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
    protected function getSchemesPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Json', 'Schemes', '*.php']);
    }

    /**
     * @inheritdoc
     */
    protected function getRuleSetsPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Json', 'Validators', '**', '*.php']);
    }

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        $settings = parent::get();

        $settings[static::KEY_URI_PREFIX] = Routes::API_URI_PREFIX;

        return $settings;
    }
}
