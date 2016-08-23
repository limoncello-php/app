<?php namespace Config\Services\Database;

use App\Database\Models\Board;
use App\Database\Models\Comment;
use App\Database\Models\Post;
use App\Database\Models\Role;
use App\Database\Models\User;

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
        $connConfig = [
            'driver'   => getenv('DB_DRIVER'),
            'host'     => getenv('DB_HOST'),
            'port'     => getenv('DB_PORT'),
            'dbname'   => getenv('DB_DATABASE'),
            'user'     => getenv('DB_USER_NAME'),
            'password' => getenv('DB_PASSWORD'),
        ];

        $inTests = getenv('APP_ENV') === 'testing';
        if ($inTests === true) {
            $connConfig['wrapperClass'] = 'Tests\Utils\TestingConnection';
        }

        $modelsList = [
            Board::class,
            Comment::class,
            Post::class,
            Role::class,
            User::class,
        ];

        return [
            DatabaseConfigInterface::CONNECTION_CONFIG => $connConfig,
            DatabaseConfigInterface::MODELS_LIST       => $modelsList,
        ];
    }
}
