<?php namespace App\Web\Controllers;

use App\Routes\WebRoutes;
use App\Web\Views;
use Limoncello\Core\Reflection\ClassIsTrait;
use Limoncello\Flute\Contracts\Http\WebControllerInterface;
use Limoncello\Flute\Contracts\Schema\SchemaInterface;
use Neomerx\JsonApi\Contracts\Document\DocumentInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
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
abstract class BaseController
{
    use ControllerTrait, ClassIsTrait;

    /**
     * @param string                 $queryRulesClass
     * @param string                 $schemaClass
     * @param string                 $apiClass
     * @param int                    $viewId
     * @param ContainerInterface     $container
     * @param ServerRequestInterface $request
     * @param array                  $viewExtraParams
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function defaultIndex(
        string $queryRulesClass,
        string $schemaClass,
        string $apiClass,
        int $viewId,
        ContainerInterface $container,
        ServerRequestInterface $request,
        array $viewExtraParams = []
    ): ResponseInterface {
        // read resources with pagination
        $parser = static::createQueryParser($container, $queryRulesClass)->parse($request->getQueryParams());
        $mapper = static::createParameterMapper($container, $schemaClass);
        $api    = static::createApi($container, $apiClass);

        $mapper->applyQueryParameters($parser, $api);

        $paginatedData = $api->index();
        $models        = $paginatedData->getData();

        // now prepare the data for rendering in HTML template
        list(DocumentInterface::KEYWORD_PREV => $prevLink,
            DocumentInterface::KEYWORD_NEXT => $nextLink) = static::getPagingLinks($request->getUri(), $paginatedData);

        // render HTML body for response
        $body = static::view($container, $viewId, $viewExtraParams + [
                'models'   => $models,
                'prevLink' => $prevLink,
                'nextLink' => $nextLink,
            ]);

        return new HtmlResponse($body);
    }

    /**
     * @param string                $index
     * @param string                $apiClass
     * @param int                   $viewId
     * @param PsrContainerInterface $container
     * @param int                   $notFoundViewId
     * @param array                 $viewExtraParams
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function defaultRead(
        string $index,
        string $apiClass,
        int $viewId,
        ContainerInterface $container,
        int $notFoundViewId = Views::NOT_FOUND_PAGE,
        array $viewExtraParams = []
    ): ResponseInterface {
        $model = static::createApi($container, $apiClass)->read($index);

        // if no data return 404
        if ($model === null) {
            return new HtmlResponse(static::view($container, $notFoundViewId), 404);
        }

        $formData = static::convertModelToFormRepresentation($container, $model);

        // render HTML body for response
        $body = static::view($container, $viewId, $viewExtraParams + ['model' => $formData]);

        return new HtmlResponse($body);
    }

    /**
     * @param array                 $inputs
     * @param string                $rulesClass
     * @param string                $schemaClass
     * @param string                $apiClass
     * @param int                   $errorViewId
     * @param PsrContainerInterface $container
     * @param array                 $viewExtraParams
     * @param string|null           $idFieldName
     * @param string                $routePrefix
     * @param string                $redirectMethod
     * @param int                   $errorStatus
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function defaultCreate(
        array $inputs,
        string $rulesClass,
        string $schemaClass,
        string $apiClass,
        int $errorViewId,
        ContainerInterface $container,
        array $viewExtraParams = [],
        string $idFieldName = null,
        string $routePrefix = WebRoutes::TOP_GROUP_PREFIX,
        string $redirectMethod = WebControllerInterface::METHOD_INDEX,
        int $errorStatus = 422
    ): ResponseInterface {
        assert(static::classImplements($schemaClass, SchemaInterface::class) === true);

        /** @var SchemaInterface $schemaClass */
        $resourceType = $schemaClass::TYPE;

        // TODO add csrf
        $validator = static::createFormValidator($container, $rulesClass);
        if ($validator->validate($inputs) === false) {
            /** @var Traversable $messages */
            $messages = $validator->getMessages();
            $errors   = iterator_to_array($messages);

            // render HTML body for response
            $body = static::view($container, $errorViewId, $viewExtraParams + [
                    'errors' => $errors,
                    'model'  => $inputs,
                ]);

            return new HtmlResponse($body, $errorStatus);
        }

        $validated = $validator->getCaptures();

        // if ID field name is given it will be used otherwise let the system assign an ID for the new resource
        if (empty($idFieldName) === false && array_key_exists($idFieldName, $validated) === true) {
            $index = $validated[$idFieldName];
            unset($validated[$idFieldName]);
        } else {
            $index = null;
        }
        $attributes = static::convertFormToModelRepresentation($container, $schemaClass, $validated);
        $toMany     = [];
        $index      = static::createApi($container, $apiClass)->create($index, $attributes, $toMany);

        $redirectUrl = static::createUrl($container, $resourceType, $redirectMethod, $index, $routePrefix);

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param string                $index
     * @param array                 $inputs
     * @param string                $rulesClass
     * @param string                $schemaClass
     * @param string                $apiClass
     * @param int                   $errorViewId
     * @param PsrContainerInterface $container
     * @param string                $routePrefix
     * @param string                $redirectMethod
     * @param int                   $notFoundViewId
     * @param int                   $errorStatus
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function defaultUpdate(
        string $index,
        array $inputs,
        string $rulesClass,
        string $schemaClass,
        string $apiClass,
        int $errorViewId,
        ContainerInterface $container,
        string $routePrefix = WebRoutes::TOP_GROUP_PREFIX,
        string $redirectMethod = WebControllerInterface::METHOD_INDEX,
        int $notFoundViewId = Views::NOT_FOUND_PAGE,
        int $errorStatus = 422
    ): ResponseInterface {
        assert(static::classImplements($schemaClass, SchemaInterface::class) === true);

        /** @var SchemaInterface $schemaClass */
        $resourceType = $schemaClass::TYPE;

        // TODO add csrf
        $validator = self::createFormValidator($container, $rulesClass);
        if ($validator->validate($inputs) === false) {
            /** @var Traversable $messages */
            $messages = $validator->getMessages();
            $errors   = iterator_to_array($messages);

            // render HTML body for response
            $body = static::view($container, $errorViewId, [
                'errors' => $errors,
                'model'  => $inputs,
            ]);

            return new HtmlResponse($body, $errorStatus);
        }

        $validated = $validator->getCaptures();

        $attributes = static::convertFormToModelRepresentation($container, $schemaClass, $validated);
        $toMany     = [];
        $updated    = static::createApi($container, $apiClass)->update($index, $attributes, $toMany);
        if ($updated <= 0) {
            return new HtmlResponse(static::view($container, $notFoundViewId), 404);
        }

        $redirectUrl = static::createUrl($container, $resourceType, $redirectMethod, $index, $routePrefix);

        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param string                $index
     * @param string                $apiClass
     * @param string                $schemaClass
     * @param PsrContainerInterface $container
     * @param string                $routePrefix
     * @param string                $redirectMethod
     * @param int                   $notFoundViewId
     *
     * @return ResponseInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function defaultDelete(
        string $index,
        string $apiClass,
        string $schemaClass,
        ContainerInterface $container,
        string $routePrefix = WebRoutes::TOP_GROUP_PREFIX,
        string $redirectMethod = WebControllerInterface::METHOD_INDEX,
        int $notFoundViewId = Views::NOT_FOUND_PAGE
    ): ResponseInterface {
        assert(static::classImplements($schemaClass, SchemaInterface::class) === true);

        /** @var SchemaInterface $schemaClass */
        $resourceType = $schemaClass::TYPE;

        $deleted = static::createApi($container, $apiClass)->remove($index);

        if ($deleted <= 0) {
            return new HtmlResponse(static::view($container, $notFoundViewId), 404);
        }

        $index       = null;
        $redirectUrl = static::createUrl($container, $resourceType, $redirectMethod, $index, $routePrefix);

        return new RedirectResponse($redirectUrl);
    }
}
