<?php namespace App;

use Limoncello\Contracts\Core\SapiInterface;
use Settings\Application as ApplicationSettings;

/**
 * @package App
 */
class Application extends \Limoncello\Application\Packages\Application\Application
{
    /**
     * @inheritdoc
     */
    public function __construct(SapiInterface $sapi = null)
    {
        parent::__construct(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . '*.php',
            ApplicationSettings::CACHE_CALLABLE,
            $sapi
        );
    }
}
