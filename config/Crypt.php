<?php namespace Config;

use Limoncello\Core\Config\ArrayConfig;

/**
 * @package Config
 */
class Crypt extends ArrayConfig
{
    /** Config key */
    const HASH_ALGORITHM = 0;

    /** Config key */
    const HASH_COST = self::HASH_ALGORITHM + 1;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct([self::class => [
            /** @see http://php.net/manual/en/password.constants.php */
            self::HASH_ALGORITHM => PASSWORD_DEFAULT,
            /** @see http://php.net/manual/en/function.password-hash.php */
            self::HASH_COST      => 10,
        ]]);
    }
}
