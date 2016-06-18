<?php namespace Config\Services\Database;

use Config\Services\Database\DatabaseInterface as DC;
use PDO;

/**
 * @package Config
 */
trait DatabaseConfig
{
    /**
     * @return array
     */
    protected function getConfig()
    {
        return [
            /** @see http://php.net/manual/en/pdo.construct.php */
            DC::USER_NAME             => getenv('DB_USER_NAME'),
            DC::PASSWORD              => getenv('DB_PASSWORD'),
            DC::PDO_CONNECTION_STRING => getenv('DB_CONNECTION_STRING'),
            DC::PDO_OPTIONS           => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ],
        ];
    }
}
