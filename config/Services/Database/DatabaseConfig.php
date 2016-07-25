<?php namespace Config\Services\Database;

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
        $config = [
            'driver'   => getenv('DB_DRIVER'),
            'host'     => getenv('DB_HOST'),
            'port'     => getenv('DB_PORT'),
            'dbname'   => getenv('DB_DATABASE'),
            'user'     => getenv('DB_USER_NAME'),
            'password' => getenv('DB_PASSWORD'),
        ];

        $inTests = getenv('IN_PHPUNIT') === '1';
        if ($inTests === true) {
            $config['wrapperClass'] = 'Tests\Utils\TestingConnection';
        }

        return $config;
    }
}
