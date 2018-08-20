<?php namespace App\Web\Controllers;

use App\Data\Models\User;
use App\Validation\Auth\SignIn;
use App\Web\Middleware\CookieAuth;
use App\Web\Views;
use Limoncello\Contracts\Cookies\CookieJarInterface;
use Limoncello\Passport\Contracts\PassportServerIntegrationInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Settings\Authorization;
use Traversable;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * @package App
 */
class AuthController extends BaseController
{
    /** Form key */
    const FORM_EMAIL = 'username';

    /** Form key */
    const FORM_PASSWORD = 'password';

    /** Form key */
    const FORM_REMEMBER = 'remember';

    /** Query parameter */
    const QUERY_SIGN_IN_REDIRECT_URI = 'redirect_uri';

    /** Route name for home page */
    const ROUTE_NAME_SIGN_IN = 'auth_sign_in';

    /** Route name for home page */
    const ROUTE_NAME_LOGOUT = 'auth_logout';

    /** Controller handler */
    const CALLABLE_SHOW_SIGN_IN = [self::class, 'showSignIn'];

    /** Controller handler */
    const CALLABLE_AUTHENTICATE = [self::class, 'authenticate'];

    /** Controller handler */
    const CALLABLE_LOGOUT = [self::class, 'signOut'];

    /**
     * @param array                  $routeParams
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     */
    public static function showSignIn(
        /** @noinspection PhpUnusedParameterInspection */
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {

        $body = static::view($container, Views::SIGN_IN_PAGE, [
            'password_min_length' => User::MIN_PASSWORD_LENGTH,
        ]);

        return new HtmlResponse($body);
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
    public static function authenticate(
        /** @noinspection PhpUnusedParameterInspection */
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        $inputs = $request->getParsedBody();
        if (is_array($inputs) === false) {
            return new HtmlResponse(static::view($container, Views::SIGN_IN_PAGE, [
                'error_message'       => 'Invalid input data.',
                'password_min_length' => User::MIN_PASSWORD_LENGTH,
            ]), 422);
        }

        // validate inputs
        $formValidator = static::createFormValidator($container, SignIn::class);
        if ($formValidator->validate($inputs) === false) {
            /** @var Traversable $errorMessages */
            $errorMessages = $formValidator->getMessages();
            $errorMessages = iterator_to_array($errorMessages);

            return new HtmlResponse(static::view($container, Views::SIGN_IN_PAGE, [
                'errors'              => $errorMessages,
                'previous'            => $inputs,
                'password_min_length' => User::MIN_PASSWORD_LENGTH,
            ]), 422);
        }
        $captures = $formValidator->getCaptures();
        list (self::FORM_EMAIL => $email, self::FORM_PASSWORD => $password) = $captures;
        $isRemember = $captures[static::FORM_REMEMBER] ?? false;
        assert(is_bool($isRemember));

        // actual check for user email and password
        /** @var PassportServerIntegrationInterface $passport */
        $passport = $container->get(PassportServerIntegrationInterface::class);
        $userId   = $passport->validateUserId($email, $password);
        if ($userId === null) {
            return new HtmlResponse(static::view($container, Views::SIGN_IN_PAGE, [
                'error_message'       => 'Invalid email or password.',
                'previous'            => $inputs,
                'password_min_length' => User::MIN_PASSWORD_LENGTH,
            ]), 401);
        }

        // if we are here name and password are valid.
        // we have to create an auth token and return its value as a cookie.

        return static::authenticateUserById(
            $userId,
            $isRemember,
            $request->getQueryParams(),
            static::getSettings($container, Authorization::class),
            static::createRouteUrl($container, HomeController::ROUTE_NAME_HOME),
            $passport,
            $container->get(CookieJarInterface::class)
        );
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
    public static function signOut(
        /** @noinspection PhpUnusedParameterInspection */
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        /** @var CookieJarInterface $cookies */
        $cookies = $container->get(CookieJarInterface::class);

        // expire at `0` means when browser closes but `1` means `January 1 1970 00:00:01 GMT`
        $cookies->create(CookieAuth::COOKIE_NAME)->setExpiresAtUnixTime(1);

        // ... and redirect to home page
        $homeUrl = static::createRouteUrl($container, HomeController::ROUTE_NAME_HOME);

        return new RedirectResponse($homeUrl);
    }

    /**
     * @param int                                $userId
     * @param bool                               $isRemember
     * @param array                              $queryParameters
     * @param array                              $authSettings
     * @param string                             $defaultRedirectUrl
     * @param PassportServerIntegrationInterface $passport
     * @param CookieJarInterface                 $cookies
     *
     * @return ResponseInterface
     */
    private static function authenticateUserById(
        int $userId,
        bool $isRemember,
        array $queryParameters,
        array $authSettings,
        string $defaultRedirectUrl,
        PassportServerIntegrationInterface $passport,
        CookieJarInterface $cookies
    ): ResponseInterface {
        // default scope of default OAuth client
        $clientScope = $passport->getClientRepository()->readScopeIdentifiers($passport->getDefaultClientIdentifier());
        // limit the default scope to user's role allowed scopes
        // by default settings it invokes `\App\Authentication\OAuth::validateScope`
        $changedScopeOrNull = $passport->verifyAllowedUserScope($userId, $clientScope);
        // now save the token with the assigned scopes
        $unsavedToken = $passport
            ->createTokenInstance()
            ->setClientIdentifier($passport->getDefaultClientIdentifier())
            ->setUserIdentifier($userId);
        if ($changedScopeOrNull === null) {
            // here will be users with scopes identical to client's ones aka unlimited (e.g. admins)
            $unsavedToken->setScopeIdentifiers($clientScope)->setScopeUnmodified();
        } else {
            // here will be less privileged users with scope less than client's default
            $unsavedToken->setScopeIdentifiers($changedScopeOrNull)->setScopeModified();
        }
        list($tokenValue, $tokenType, $tokenExpiresIn, $refreshValue) = $passport->generateTokenValues($unsavedToken);
        $unsavedToken->setValue($tokenValue)->setType($tokenType)->setRefreshValue($refreshValue);
        $savedToken     = $passport->getTokenRepository()->createToken($unsavedToken);
        $valueForCookie = $savedToken->getValue();

        // now cookie ...
        $authCookie = $cookies
            ->create(CookieAuth::COOKIE_NAME)
            ->setValue($valueForCookie)
            ->setAccessibleOnlyThroughHttp();

        $isRemember === true ? $authCookie->setExpiresInSeconds($tokenExpiresIn) : $authCookie->setExpiresAtUnixTime(0);

        $isOnlyHttpsCookie = $authSettings[Authorization::KEY_AUTH_COOKIE_ONLY_OVER_HTTPS];
        if ($isOnlyHttpsCookie === true) {
            $authCookie->setSendOnlyOverSecureConnection();
        }

        $redirectUrl = null;
        if (array_key_exists(static::QUERY_SIGN_IN_REDIRECT_URI, $queryParameters) === true) {
            $mightBeRedirectUrl = $queryParameters[static::QUERY_SIGN_IN_REDIRECT_URI];
            if (is_string($mightBeRedirectUrl) === true && empty($mightBeRedirectUrl) === false) {
                $redirectUrl = static::safelyParseRedirectUrl($mightBeRedirectUrl);
            }
        }

        // ... and redirect to home page if no valid redirect URI given
        if ($redirectUrl === null) {
            $redirectUrl = $defaultRedirectUrl;
        }

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param string $mightBeUrl
     *
     * @return string
     */
    private static function safelyParseRedirectUrl(string $mightBeUrl): string
    {
        $url = '';

        $parsed = parse_url($mightBeUrl);
        if ($parsed !== false) {
            $url =
                (array_key_exists('path', $parsed) === true ? $parsed['path'] : '/') .
                (array_key_exists('query', $parsed) === true ? '?' . $parsed['query'] : '') .
                (array_key_exists('fragment', $parsed) === true ? '#' . $parsed['fragment'] : '');
        }

        if (empty($url) === true || $url[0] !== '/') {
            $url = '/' . $url;
        }

        return $url;
    }
}
