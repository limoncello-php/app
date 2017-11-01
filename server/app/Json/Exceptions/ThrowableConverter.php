<?php namespace App\Json\Exceptions;

use Limoncello\Application\Exceptions\AuthorizationException;
use Limoncello\Flute\Contracts\Exceptions\JsonApiThrowableConverterInterface;
use Limoncello\Passport\Exceptions\AuthenticationException;
use Neomerx\JsonApi\Exceptions\ErrorCollection;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Throwable;

/**
 * @package App
 */
class ThrowableConverter implements JsonApiThrowableConverterInterface
{
    /**
     * @inheritdoc
     *
     * This code provides an ability to transform various exceptions in API (application specific,
     * authorization, 3rd party, etc) and convert it to JSON API error.
     */
    public static function convert(Throwable $throwable): ?JsonApiException
    {
        $converted = null;

        if ($throwable instanceof AuthorizationException) {
            $httpCode  = 403;
            $action    = $throwable->getAction();
            $errors    = (new ErrorCollection())
                ->addDataError(
                    'Unauthorized',
                    "You are not unauthorized for action `$action`.",
                    null,
                    null,
                    null,
                    $httpCode
                );
            $converted = new JsonApiException($errors, $httpCode, $throwable);
        } elseif ($throwable instanceof AuthenticationException) {
            $httpCode  = 401;
            $errors    = (new ErrorCollection())
                ->addDataError(
                    'Authentication failed',
                    'Authentication failed',
                    null,
                    null,
                    null,
                    $httpCode
                );
            $converted = new JsonApiException($errors, $httpCode, $throwable);
        }

        return $converted;
    }
}
