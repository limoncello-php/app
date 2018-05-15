<?php namespace App\Web\Controllers;

use App\Api\RolesApi;
use App\Json\Schemas\RoleSchema;
use App\Json\Schemas\UserSchema;
use App\Validation\Role\RoleCreateForm;
use App\Validation\Role\RolesReadQuery;
use App\Validation\Role\RoleUpdateForm;
use App\Web\Views;
use Limoncello\Contracts\Exceptions\AuthorizationExceptionInterface;
use Limoncello\Flute\Contracts\Http\WebControllerInterface;
use Limoncello\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class RolesController extends BaseController implements WebControllerInterface
{
    /** Controller handler */
    const CALLABLE_READ_USERS = [self::class, 'readUsers'];

    /** Route name */
    const ROUTE_NAME_READ_USERS = 'roles::readUsers';

    /**
     * @inheritdoc
     */
    public static function index(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        // Default index implementation
        // - parses HTTP query parameters for filters, sorts and pagination parameters
        // - checks the query parameters comply with validation rules defined by a developer
        // - reads data from the database with filters, sorts and pagination applied via API specified
        // - generates HTML response with template specified (including pagination links)
        //
        // Consider it as a starting point and feel free to modify the code here and
        // in `BaseController` to meet your requirements.

        $queryRules = RolesReadQuery::class;
        $dataSchema = RoleSchema::class;
        $apiClass   = RolesApi::class;
        $templateId = Views::ROLES_INDEX_PAGE;

        $extraParams = [
            'url_to_create' => static::createUrl($container, RoleSchema::TYPE, static::METHOD_INSTANCE),
        ];

        return self::defaultIndex($queryRules, $dataSchema, $apiClass, $templateId, $container, $request, $extraParams);
    }

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function instance(/** @noinspection PhpUnusedParameterInspection */
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        $params = [
            'post_create_url' => static::createUrl($container, RoleSchema::TYPE, self::METHOD_CREATE),
        ];
        $body   = self::view($container, Views::ROLE_MODIFY_PAGE, $params);

        return new HtmlResponse($body);
    }

    /**
     * @inheritdoc
     */
    public static function create(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        // Default read implementation
        // - reads and validates input data
        // - on successful validation creates a resource via API specified
        // - redirects to resource page
        //
        // Consider it as a starting point and feel free to modify the code here and
        // in `BaseController` to meet your requirements.
        $inputs          = $request->getParsedBody();
        $validationRules = RoleCreateForm::class;
        $apiClass        = RolesApi::class;
        $schemaClass     = RoleSchema::class;
        $errorViewId     = Views::ROLE_MODIFY_PAGE;
        $viewParams      = [
            'post_create_url' => static::createUrl($container, RoleSchema::TYPE, self::METHOD_CREATE),
        ];
        $roleIdName      = RoleSchema::RESOURCE_ID;

        return self::defaultCreate(
            $inputs,
            $validationRules,
            $schemaClass,
            $apiClass,
            $errorViewId,
            $container,
            $viewParams,
            $roleIdName
        );
    }

    /**
     * @inheritdoc
     */
    public static function read(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        // Default read implementation
        // - reads data from the database with required relationships via API specified
        // - generates HTML response with template specified
        //
        // Consider it as a starting point and feel free to modify the code here and
        // in `BaseController` to meet your requirements.

        $index              = $routeParams[static::ROUTE_KEY_INDEX];
        $apiClass           = RolesApi::class;
        $templateId         = Views::ROLE_MODIFY_PAGE;
        $notFoundTemplateId = Views::NOT_FOUND_PAGE;
        $viewParams         = [
            'post_update_url' => static::createUrl($container, RoleSchema::TYPE, self::METHOD_UPDATE, $index),
            'post_delete_url' => static::createUrl($container, RoleSchema::TYPE, self::METHOD_DELETE, $index),
        ];

        return self::defaultRead($index, $apiClass, $templateId, $container, $notFoundTemplateId, $viewParams);
    }

    /**
     * @inheritdoc
     */
    public static function update(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        // Default read implementation
        // - reads and validates input data
        // - on successful validation updates the resource via API specified
        // - redirects to resource page
        //
        // Consider it as a starting point and feel free to modify the code here and
        // in `BaseController` to meet your requirements.
        $index           = $routeParams[static::ROUTE_KEY_INDEX];
        $inputs          = $request->getParsedBody();
        $validationRules = RoleUpdateForm::class;
        $apiClass        = RolesApi::class;
        $schemaClass     = RoleSchema::class;
        $errorViewId     = Views::ROLE_MODIFY_PAGE;

        return self::defaultUpdate($index, $inputs, $validationRules, $schemaClass, $apiClass, $errorViewId, $container);
    }

    /**
     * @inheritdoc
     */
    public static function delete(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        // Default delete implementation
        // - deletes resource by its index via API specified
        // - redirects to resource index page by its type
        //
        // Consider it as a starting point and feel free to modify the code here and
        // in `BaseController` to meet your requirements.

        $index = $routeParams[static::ROUTE_KEY_INDEX];

        return self::defaultDelete($index, RolesApi::class, RoleSchema::class, $container);
    }

    /**
     * @inheritdoc
     *
     * @throws AuthorizationExceptionInterface
     */
    public static function readUsers(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        $roleName = $routeParams[static::ROUTE_KEY_INDEX];

        $parser = static::createQueryParser($container, DefaultQueryValidationRules::class)
            ->parse($request->getQueryParams());
        $mapper = static::createParameterMapper($container, RoleSchema::class);
        /** @var RolesApi $api */
        $api = static::createApi($container, RolesApi::class);

        $mapper->applyQueryParameters($parser, $api);

        $paginatedData = $api->readUsers($roleName);
        $users         = $paginatedData->getData();

        // now prepare the data for rendering in HTML template
        list(DocumentInterface::KEYWORD_PREV => $prevLink,
            DocumentInterface::KEYWORD_NEXT => $nextLink) = static::getPagingLinks($request->getUri(), $paginatedData);

        // render HTML body for response
        $body = static::view($container, Views::USERS_INDEX_PAGE, [
            'models'          => $users,
            'prevLink'        => $prevLink,
            'nextLink'        => $nextLink,
            'base_modify_url' => static::createUrl($container, UserSchema::TYPE, UsersController::METHOD_READ, ''),
            'url_to_create'   => static::createUrl($container, UserSchema::TYPE, static::METHOD_INSTANCE),
        ]);

        return new HtmlResponse($body);
    }
}
