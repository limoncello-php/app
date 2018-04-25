<?php namespace Settings;

use Dotenv\Dotenv;
use Limoncello\Crypt\Package\HasherSettings;

/**
 * @package Settings
 */
class Hasher extends HasherSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        return [

                /** @see http://php.net/manual/en/function.password-hash.php */
                static::KEY_COST => 10,

            ] + parent::getSettings();
    }
}
