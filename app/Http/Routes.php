<?php namespace App\Http;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\BoardsController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\HomeController;
use App\Schemes\BoardSchema;
use App\Schemes\PostSchema;
use App\Schemes\RoleSchema;
use App\Schemes\UserSchema;
use Limoncello\Core\Contracts\Routing\GroupInterface as GI;
use Limoncello\Core\Contracts\Routing\GroupInterface;
use Limoncello\Core\Contracts\Routing\RouteInterface as RI;
use Limoncello\Core\Contracts\Routing\RouteInterface;
use Limoncello\Core\Routing\Group;
use Limoncello\JsonApi\Contracts\Http\ControllerInterface;
use Limoncello\JsonApi\Contracts\Schema\SchemaInterface;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;

/**
 * @package App
 */
trait Routes
{
    /**
     * This middleware will be executed on every request even when no matching route is found.
     *
     * @return callable[]
     */
    protected function getGlobalMiddleware()
    {
        return [
            Middleware\Cors::class . '::handle',
        ];
    }

    /**
     * @return GI
     */
    protected function getRoutes()
    {
        // This closure automates adding routes for CRUD operations.
        // As routes are cached it does not slow down the app.
        $addResource = function (GroupInterface $group, $controllerClass) {
            /** @var BaseController $controllerClass */
            /** @var SchemaInterface $schemaClass */
            $schemaClass = $controllerClass::SCHEMA_CLASS;
            $subUri      = $schemaClass::TYPE;
            /** @var string $controllerClass */
            /** @var string $schemaClass */

            // methods that changes data (create/update/delete) require authentication (middleware)
            $authParams = [
                RouteInterface::PARAM_MIDDLEWARE_LIST => [Middleware\AuthenticationRequired::class . '::handle']
            ];

            $indexSlug = '/{' . BaseController::ROUTE_KEY_INDEX . '}';
            $group
                ->get($subUri, [$controllerClass, ControllerInterface::METHOD_INDEX])
                ->post($subUri, [$controllerClass, ControllerInterface::METHOD_CREATE], $authParams)
                ->get($subUri . $indexSlug, [$controllerClass, ControllerInterface::METHOD_READ])
                ->patch($subUri . $indexSlug, [$controllerClass, ControllerInterface::METHOD_UPDATE], $authParams)
                ->delete($subUri . $indexSlug, [$controllerClass, ControllerInterface::METHOD_DELETE], $authParams);
        };

        $addRelationship = function (GroupInterface $group, $relationshipName, $controllerClass, $method) {
            /** @var BaseController $controllerClass */
            /** @var SchemaInterface $schemaClass */
            $schemaClass = $controllerClass::SCHEMA_CLASS;
            $subUri      = $schemaClass::TYPE;
            /** @var string $controllerClass */
            /** @var string $schemaClass */

            // `related` URI is needed for ember to pick up actual relationship resources when we send URLs.
            $resourceIdUri = $subUri . '/{' . BaseController::ROUTE_KEY_INDEX . '}/';
            $selfUri       = $resourceIdUri . DocumentInterface::KEYWORD_RELATIONSHIPS . '/' . $relationshipName;
            $relatedUri    = $resourceIdUri . $relationshipName;

            $group->get($selfUri, [$controllerClass, $method]);
            $group->get($relatedUri, [$controllerClass, $method]);
        };

        return (new Group())
            // This handler is very simple and we don't need `ServerRequestInterface`
            // so we can instruct not to create it for us.
            ->get('/', [HomeController::class, 'index'], [
                RI::PARAM_REQUEST_FACTORY         => null,
                RI::PARAM_CONTAINER_CONFIGURATORS => [HomeController::class . '::welcomeConfigurator'],
            ])
            ->post('/authenticate', [UsersController::class, 'authenticate'], [
                RI::PARAM_CONTAINER_CONFIGURATORS => [BaseController::class . '::containerConfigurator'],
            ])

            ->group(BaseController::API_URI_PREFIX, function (GroupInterface $group) use (
                $addResource,
                $addRelationship
            ) {

                $addResource($group, BoardsController::class);
                $addRelationship($group, BoardSchema::REL_POSTS, BoardsController::class, 'readPosts');

                $addResource($group, CommentsController::class);

                $addResource($group, PostsController::class);
                $addRelationship($group, PostSchema::REL_COMMENTS, PostsController::class, 'readComments');

                $addResource($group, RolesController::class);
                $addRelationship($group, RoleSchema::REL_USERS, RolesController::class, 'readUsers');

                $addResource($group, UsersController::class);
                $addRelationship($group, UserSchema::REL_POSTS, UsersController::class, 'readPosts');
                $addRelationship($group, UserSchema::REL_COMMENTS, UsersController::class, 'readComments');
            }, [
                GroupInterface::PARAM_CONTAINER_CONFIGURATORS => [BaseController::class . '::containerConfigurator'],
                GroupInterface::PARAM_MIDDLEWARE_LIST => [Middleware\TokenAuthentication::class . '::handle'],
            ]);
    }
}
