<?php namespace App\Web\Controllers;

use App\Data\Models\Role;
use App\Data\Models\User;
use App\Data\Seeds\PassportSeed;
use App\Json\Schemas\RoleSchema;
use App\Json\Schemas\UserSchema;
use App\Routes\WebRoutes;
use App\Web\Views;
use Limoncello\Contracts\Application\ApplicationConfigurationInterface as A;
use Limoncello\Contracts\Application\CacheSettingsProviderInterface;
use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Contracts\Data\ModelSchemaInfoInterface;
use Limoncello\Contracts\Data\RelationshipTypes;
use Limoncello\Contracts\Http\RequestStorageInterface;
use Limoncello\Contracts\L10n\FormatterFactoryInterface;
use Limoncello\Contracts\L10n\FormatterInterface;
use Limoncello\Contracts\Passport\PassportAccountInterface;
use Limoncello\Contracts\Passport\PassportAccountManagerInterface;
use Limoncello\Contracts\Routing\RouterInterface;
use Limoncello\Contracts\Settings\SettingsProviderInterface;
use Limoncello\Contracts\Templates\TemplatesInterface;
use Limoncello\Flute\Contracts\Http\WebControllerInterface;
use Limoncello\Flute\Contracts\Models\PaginatedDataInterface;
use Limoncello\Flute\Contracts\Schema\JsonSchemasInterface;
use Limoncello\Flute\Contracts\Schema\SchemaInterface;
use Limoncello\Flute\Contracts\Validation\FormValidatorFactoryInterface;
use Limoncello\Flute\Contracts\Validation\FormValidatorInterface;
use Limoncello\Flute\Contracts\Validation\JsonApiQueryParserInterface;
use Limoncello\Flute\Http\Traits\DefaultControllerMethodsTrait;
use Limoncello\Flute\Http\Traits\FluteRoutesTrait;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Neomerx\JsonApi\Contracts\Http\Query\BaseQueryParserInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\UriInterface;

/**
 * @package App
 */
trait ControllerTrait
{
    use DefaultControllerMethodsTrait {
        defaultCreateApi as createApi;
        defaultCreateQueryParser as createQueryParser;
        defaultCreateParameterMapper as createParameterMapper;

        defaultCreate as private defaultCreateJsonApi;
        defaultUpdate as private defaultUpdateJsonApi;
    }

    use FluteRoutesTrait {
        apiController as private;
        webController as private;
        relationship as private;
    }

    /**
     * @param UriInterface           $originalUri
     * @param PaginatedDataInterface $data
     *
     * @return UriInterface[]
     */
    protected static function getPagingLinks(UriInterface $originalUri, PaginatedDataInterface $data): array
    {
        $links = [
            DocumentInterface::KEYWORD_PREV => null,
            DocumentInterface::KEYWORD_NEXT => null,
        ];

        if ($data->isCollection() === true && (0 < $data->getOffset() || $data->hasMoreItems() === true)) {
            parse_str($originalUri->getQuery(), $queryParams);

            $pageSize    = $data->getLimit();
            $linkClosure = function (int $offset) use ($originalUri, $pageSize, $queryParams): UriInterface {
                $paramsWithPaging = array_merge($queryParams, [
                    BaseQueryParserInterface::PARAM_PAGE => [
                        JsonApiQueryParserInterface::PARAM_PAGING_OFFSET => $offset,
                        JsonApiQueryParserInterface::PARAM_PAGING_LIMIT  => $pageSize,
                    ],
                ]);

                $paginatedUri = $originalUri->withQuery(http_build_query($paramsWithPaging));

                return $paginatedUri;
            };

            if ($data->getOffset() > 0) {
                $prevOffset                             = max(0, $data->getOffset() - $data->getLimit());
                $links[DocumentInterface::KEYWORD_PREV] = $linkClosure($prevOffset);
            }
            if ($data->hasMoreItems() === true) {
                $nextOffset                             = $data->getOffset() + $data->getLimit();
                $links[DocumentInterface::KEYWORD_NEXT] = $linkClosure($nextOffset);
            }
        }

        return $links;
    }

    /**
     * @param ContainerInterface $container
     * @param int                $viewId
     * @param array              $parameters
     * @param string             $viewsNamespace
     *
     * @return string
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function view(
        ContainerInterface $container,
        int $viewId,
        array $parameters = [],
        string $viewsNamespace = Views::NAMESPACE
    ): string {
        $formatter    = static::createFormatter($container, $viewsNamespace);
        $templateName = $formatter->formatMessage($viewId);

        /** @var TemplatesInterface $templates */
        $templates = $container->get(TemplatesInterface::class);

        /** @var CacheSettingsProviderInterface $provider */
        $provider  = $container->get(CacheSettingsProviderInterface::class);
        $originUri = $provider->getApplicationConfiguration()[A::KEY_APP_ORIGIN_URI];

        $currentUser = static::getCurrentUser($container);

        $curHostName = static::getHostUri($container);

        $isSignedIn = $currentUser !== null;
        if ($isSignedIn === true) {
            $firstName = $currentUser->getProperty(User::FIELD_FIRST_NAME);
            $lastName  = $currentUser->getProperty(User::FIELD_LAST_NAME);
            $roleName  = $currentUser->getProperty(Role::FIELD_ID);

            $allowedScopes  = $currentUser->getScopes();
            $canAdminUsers  = in_array(PassportSeed::SCOPE_ADMIN_USERS, $allowedScopes);
            $canAdminRoles  = in_array(PassportSeed::SCOPE_ADMIN_ROLES, $allowedScopes);
            $canViewUsers   = in_array(PassportSeed::SCOPE_VIEW_USERS, $allowedScopes);
            $canViewRoles   = in_array(PassportSeed::SCOPE_VIEW_ROLES, $allowedScopes);
            $indexMethod    = WebControllerInterface::METHOD_INDEX;
            $usersRouteName = static::routeName(WebRoutes::TOP_GROUP_PREFIX, UserSchema::TYPE, $indexMethod);
            $rolesRouteName = static::routeName(WebRoutes::TOP_GROUP_PREFIX, RoleSchema::TYPE, $indexMethod);
            $usersUrl       = $canViewUsers === true ? static::createRouteUrl($container, $usersRouteName) : null;
            $rolesUrl       = $canViewRoles === true ? static::createRouteUrl($container, $rolesRouteName) : null;

            $signInUrl  = null;
            $signOutUrl = static::createRouteUrl($container, AuthController::ROUTE_NAME_LOGOUT);
        } else {
            $firstName     = null;
            $lastName      = null;
            $roleName      = null;
            $canAdminUsers = false;
            $canAdminRoles = false;
            $canViewUsers  = false;
            $canViewRoles  = false;
            $usersUrl      = null;
            $rolesUrl      = null;
            $signInUrl     = static::createRouteUrl($container, AuthController::ROUTE_NAME_SIGN_IN);
            $signOutUrl    = null;
        }

        $defaultParams = [
            '_origin_uri' => $originUri,
            'host_name'   => $curHostName,

            'is_signed_in' => $isSignedIn,
            'sign_in_url'  => $signInUrl,
            'sign_out_url' => $signOutUrl,

            'first_name'      => $firstName,
            'last_name'       => $lastName,
            'role_name'       => $roleName,
            'can_admin_users' => $canAdminUsers,
            'can_admin_roles' => $canAdminRoles,
            'can_view_users'  => $canViewUsers,
            'can_view_roles'  => $canViewRoles,
            'users_url'       => $usersUrl,
            'roles_url'       => $rolesUrl,
        ];

        $body = $templates->render($templateName, $parameters + $defaultParams);

        return $body;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $rulesClass
     *
     * @return FormValidatorInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function createFormValidator(
        ContainerInterface $container,
        string $rulesClass
    ): FormValidatorInterface {
        /** @var FormValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $container->get(FormValidatorFactoryInterface::class);
        $validator        = $validatorFactory->createValidator($rulesClass);

        return $validator;
    }

    /**
     * @param ContainerInterface $container
     * @param ModelInterface     $model
     *
     * @return array|null
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function convertModelToFormRepresentation(
        ContainerInterface $container,
        ModelInterface $model
    ): ?array {
        /** @var JsonSchemasInterface $schemas */
        $schemas = $container->get(JsonSchemasInterface::class);
        /** @var SchemaInterface $schema */
        $schema = $schemas->getSchema($model);
        assert($schema instanceof SchemaInterface);

        $mappings = $schema::getMappings();

        $formData = [];
        if (array_key_exists(SchemaInterface::SCHEMA_ATTRIBUTES, $mappings) === true) {
            foreach ($mappings[SchemaInterface::SCHEMA_ATTRIBUTES] as $jsonAttrName => $modelAttrName) {
                $formData[$jsonAttrName] = $model->{$modelAttrName} ?? null;
            }
        }
        if (array_key_exists(SchemaInterface::SCHEMA_RELATIONSHIPS, $mappings) === true) {
            /** @var ModelSchemaInfoInterface $modelSchemeInfo */
            $modelSchemeInfo = $container->get(ModelSchemaInfoInterface::class);
            $modelClass      = get_class($model);
            foreach ($mappings[SchemaInterface::SCHEMA_RELATIONSHIPS] as $jsonRelName => $modelRelName) {
                $relationshipType = $modelSchemeInfo->getRelationshipType($modelClass, $modelRelName);
                if ($relationshipType === RelationshipTypes::BELONGS_TO) {
                    $fkName                 = $modelSchemeInfo->getForeignKey($modelClass, $modelRelName);
                    $formData[$jsonRelName] = $model->{$fkName} ?? null;
                }
            }
        }

        return $formData;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $schemaClass
     * @param array              $formData
     *
     * @return array
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function convertFormToModelRepresentation(
        ContainerInterface $container,
        string $schemaClass,
        array $formData
    ): array {
        assert(self::classImplements($schemaClass, SchemaInterface::class));

        /** @var SchemaInterface $schemaClass */
        $modelClass = $schemaClass::MODEL;
        assert(empty($modelClass) === false);

        $attributes = [];

        /** @var ModelSchemaInfoInterface $modelSchemeInfo */
        $modelSchemeInfo = $container->get(ModelSchemaInfoInterface::class);
        foreach ($formData as $jsonName => $value) {
            if ($schemaClass::hasAttributeMapping($jsonName) === true) {
                $modelAttrName              = $schemaClass::getAttributeMapping($jsonName);
                $attributes[$modelAttrName] = $value;
            } elseif ($schemaClass::hasRelationshipMapping($jsonName) === true) {
                $modelRelName        = $schemaClass::getRelationshipMapping($jsonName);
                $fkName              = $modelSchemeInfo->getForeignKey($modelClass, $modelRelName);
                $attributes[$fkName] = $value;
            }
        }

        return $attributes;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $namespace
     * @param string|null        $locale
     *
     * @return FormatterInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function createFormatter(
        ContainerInterface $container,
        string $namespace,
        string $locale = null
    ): FormatterInterface {
        /** @var FormatterFactoryInterface $factory */
        $factory   = $container->get(FormatterFactoryInterface::class);
        $formatter = $locale === null ?
            $factory->createFormatter($namespace) : $factory->createFormatterForLocale($namespace, $locale);

        return $formatter;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return PassportAccountInterface|null
     */
    protected static function getCurrentUser(ContainerInterface $container): ?PassportAccountInterface
    {
        $curAccount = null;

        try {
            /** @var PassportAccountManagerInterface $manager */
            $manager    = $container->get(PassportAccountManagerInterface::class);
            $curAccount = $manager->getPassport();
        } catch (ContainerExceptionInterface | NotFoundExceptionInterface $exception) {
            assert(false, 'Container do not have `' . PassportAccountManagerInterface::class . '`.');
        }

        return $curAccount;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $settingsClass
     *
     * @return array
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function getSettings(ContainerInterface $container, string $settingsClass): array
    {
        /** @var SettingsProviderInterface $provider */
        $provider = $container->get(SettingsProviderInterface::class);
        $settings = $provider->get($settingsClass);

        return $settings;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $type
     * @param string             $method
     * @param string|null        $index
     * @param string             $routePrefix
     *
     * @return string
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function createUrl(
        ContainerInterface $container,
        string $type,
        string $method,
        string $index = null,
        string $routePrefix = WebRoutes::TOP_GROUP_PREFIX
    ): string {
        $routeName = static::routeName($routePrefix, $type, $method);
        $result    = $index === null ?
            static::createRouteUrl($container, $routeName) :
            static::createRouteUrl($container, $routeName, [WebControllerInterface::ROUTE_KEY_INDEX => $index]);

        return $result;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $routeName
     * @param array              $placeholders
     * @param array              $queryParams
     *
     * @return string
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function createRouteUrl(
        ContainerInterface $container,
        string $routeName,
        array $placeholders = [],
        array $queryParams = []
    ): string {
        /** @var RouterInterface $router */
        $router = $container->get(RouterInterface::class);

        $hostUri = static::getHostUri($container);
        $url     = $router->get($hostUri, $routeName, $placeholders, $queryParams);

        return $url;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return string
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function getHostUri(ContainerInterface $container): string
    {
        /** @var RequestStorageInterface $curRequestStorage */
        $curRequestStorage = $container->get(RequestStorageInterface::class);
        $curRequestUri     = $curRequestStorage->get()->getUri();

        $scheme = $curRequestUri->getScheme();
        $host   = $curRequestUri->getHost();
        $port   = $curRequestUri->getPort();

        $result = $port === null || $port === 80 ? "$scheme://$host" : "$scheme://$host:$port";

        return $result;
    }
}
