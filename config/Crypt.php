<?php namespace Config;

use App\Contracts\Config\Crypt as C;
use Limoncello\Core\Config\ArrayConfig;

/**
 * @package Config
 */
class Crypt extends ArrayConfig
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct([C::class => [
            /** @see http://php.net/manual/en/password.constants.php */
            C::HASH_ALGORITHM => PASSWORD_DEFAULT,
            /** @see http://php.net/manual/en/function.password-hash.php */
            C::HASH_COST      => 10,
        ]]);
    }
}
