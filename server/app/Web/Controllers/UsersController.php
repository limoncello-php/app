<?php namespace App\Web\Controllers;

use App\Api\RolesApi;
use App\Api\UsersApi;
use App\Data\Seeds\RolesSeed;
use App\Json\Schemes\UserSchema;
use App\Validation\ErrorCodes;
use App\Validation\User\UserCreateForm;
use App\Validation\User\UsersReadQuery;
use App\Validation\User\UserUpdateForm;
use App\Web\Views;
use Limoncello\Flute\Contracts\Http\WebControllerInterface;
use Limoncello\Flute\Package\FluteSettings;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Traversable;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class UsersController extends BaseController implements WebControllerInterface
{
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

        $queryRules = UsersReadQuery::class;
        $dataSchema = UserSchema::class;
        $apiClass   = UsersApi::class;
        $templateId = Views::USERS_INDEX_PAGE;

        $extraParams = [
            'base_modify_url' => static::createUrl($container, UserSchema::TYPE, static::METHOD_READ, ''),
            'url_to_create'   => static::createUrl($container, UserSchema::TYPE, static::METHOD_INSTANCE),
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
            'post_create_url' => static::createUrl($container, UserSchema::TYPE, self::METHOD_CREATE),
            'roles'           => static::createApi($container, RolesApi::class)->index()->getData(),
            'model'           => [UserSchema::REL_ROLE => RolesSeed::ROLE_USER],
        ];

        $body = self::view($container, Views::USER_MODIFY_PAGE, $params);

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
        $inputs = $request->getParsedBody();
        list ($inputs, $validated, $errors) = static::validateUserInputs($container, $inputs, UserCreateForm::class);

        if ($errors !== null) {
            // render HTML body for response
            $body = static::view($container, Views::USER_MODIFY_PAGE, [
                'post_create_url' => static::createUrl($container, UserSchema::TYPE, self::METHOD_CREATE),
                'roles'           => static::createApi($container, RolesApi::class)->index()->getData(),
                'model'           => $inputs,
                'errors'          => $errors,
            ]);

            return new HtmlResponse($body, 422);
        }

        $index      = null;
        $attributes = static::convertFormToModelRepresentation($container, UserSchema::class, $validated);
        $toMany     = [];
        $index      = static::createApi($container, UsersApi::class)->create($index, $attributes, $toMany);
        assert(empty($index) === false);

        $redirectUrl = static::createUrl($container, UserSchema::TYPE, self::METHOD_INDEX);

        return new RedirectResponse($redirectUrl);
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
        $apiClass           = UsersApi::class;
        $templateId         = Views::USER_MODIFY_PAGE;
        $notFoundTemplateId = Views::NOT_FOUND_PAGE;
        $viewParams         = [
            'post_update_url' => static::createUrl($container, UserSchema::TYPE, self::METHOD_UPDATE, $index),
            'post_delete_url' => static::createUrl($container, UserSchema::TYPE, self::METHOD_DELETE, $index),
            'roles'           => static::createApi($container, RolesApi::class)->index()->getData(),
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
        $inputs = $request->getParsedBody();
        $index  = $routeParams[self::ROUTE_KEY_INDEX];

        list ($inputs, $validated, $errors) = static::validateUserInputs($container, $inputs, UserUpdateForm::class);

        if ($errors !== null) {
            // render HTML body for response
            $body = static::view($container, Views::USER_MODIFY_PAGE, [
                'post_update_url' => static::createUrl($container, UserSchema::TYPE, self::METHOD_UPDATE, $index),
                'post_delete_url' => static::createUrl($container, UserSchema::TYPE, self::METHOD_DELETE, $index),
                'roles'           => static::createApi($container, RolesApi::class)->index()->getData(),
                'model'           => $inputs,
                'errors'          => $errors,
            ]);

            return new HtmlResponse($body, 422);
        }

        // if no password was given (empty) remove the input
        if (empty($validated[UserSchema::CAPTURE_NAME_PASSWORD]) === true) {
            unset($validated[UserSchema::CAPTURE_NAME_PASSWORD]);
        }

        $attributes = static::convertFormToModelRepresentation($container, UserSchema::class, $validated);
        $toMany     = [];
        $updated    = static::createApi($container, UsersApi::class)->update($index, $attributes, $toMany);
        if ($updated <= 0) {
            return new HtmlResponse(static::view($container, Views::NOT_FOUND_PAGE), 404);
        }

        $redirectUrl = static::createUrl($container, UserSchema::TYPE, self::METHOD_INDEX);

        return new RedirectResponse($redirectUrl);
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

        return self::defaultDelete($index, UsersApi::class, UserSchema::class, $container);
    }

    /**
     * @param ContainerInterface $container
     * @param array              $inputs
     * @param string             $rulesClass
     *
     * @return array
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private static function validateUserInputs(ContainerInterface $container, array $inputs, string $rulesClass): array
    {
        $errors    = null;
        $validated = null;

        // TODO add csrf
        $isPasswordSame =
            ($inputs[UserSchema::CAPTURE_NAME_PASSWORD] ?? null) ===
            ($inputs[UserSchema::CAPTURE_NAME_PASSWORD_CONFIRMATION] ?? null);

        $validator = self::createFormValidator($container, $rulesClass);
        if ($validator->validate($inputs) === false || $isPasswordSame === false) {
            /** @var Traversable $messages */
            $messages = $validator->getMessages();
            $errors   = iterator_to_array($messages);
            if ($isPasswordSame === false) {
                $formatter = static::createFormatter($container, FluteSettings::VALIDATION_NAMESPACE);

                $errors[UserSchema::CAPTURE_NAME_PASSWORD_CONFIRMATION] =
                    $formatter->formatMessage(ErrorCodes::CONFIRMATION_SHOULD_MATCH_PASSWORD);
            }

            // clear password from inputs
            unset($inputs[UserSchema::CAPTURE_NAME_PASSWORD]);
            unset($inputs[UserSchema::CAPTURE_NAME_PASSWORD_CONFIRMATION]);
        } else {
            $validated = $validator->getCaptures();
            unset($validated[UserSchema::CAPTURE_NAME_PASSWORD_CONFIRMATION]);
        }

        return [$inputs, $validated, $errors];
    }
}
