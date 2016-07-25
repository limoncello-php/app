<?php namespace Config\Services\JsonApi;

use App\Database\Models\Board;
use App\Database\Models\Comment;
use App\Database\Models\Post;
use App\Database\Models\Role;
use App\Database\Models\User;
use App\Http\Controllers\BaseController;
use App\Schemes\BoardSchema;
use App\Schemes\CommentSchema;
use App\Schemes\PostSchema;
use App\Schemes\RoleSchema;
use App\Schemes\UserSchema;

/**
 * @package Config
 */
trait JsonApiConfig
{
    /**
     * @return array
     */
    protected function getConfig()
    {
        $config = new \Limoncello\JsonApi\Config\JsonApiConfig();
        $config
            ->setModelSchemaMap([
                Board::class   => BoardSchema::class,
                Comment::class => CommentSchema::class,
                Post::class    => PostSchema::class,
                Role::class    => RoleSchema::class,
                User::class    => UserSchema::class,
            ])
            ->setRelationshipPagingSize(10)
            ->setJsonEncodeOptions($config->getJsonEncodeOptions() | JSON_PRETTY_PRINT)
            ->setHideVersion()
            ->setMeta([
                'name'       => 'JSON API Demo Application',
                'copyright'  => '2015-2016 info@neomerx.com',
                'powered-by' => 'Limoncello flute',
            ])
            ->setUriPrefix(BaseController::API_URI_PREFIX);

        $data = $config->getConfig();

        return $data;
    }
}
