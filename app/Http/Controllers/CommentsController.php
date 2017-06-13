<?php namespace App\Http\Controllers;

use App\Data\Models\Comment as Model;
use App\Json\Api\CommentsApi as Api;
use App\Json\Schemes\CommentScheme as Scheme;
use App\Json\Validators\CommentsValidator as Validator;
use Limoncello\Flute\Http\BaseController;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class CommentsController extends BaseController
{
    /** @inheritdoc */
    const API_CLASS = Api::class;

    /** @inheritdoc */
    const SCHEMA_CLASS = Scheme::class;

    /**
     * @inheritdoc
     */
    public static function parseInputOnCreate(
        ContainerInterface $container,
        ServerRequestInterface $request
    ): array {
        return static::prepareCaptures(
            Validator::onCreateValidator($container)
                ->assert(static::parseJson($container, $request))
                ->getCaptures(),
            Model::FIELD_ID,
            Validator::captureNames()
        );
    }

    /**
     * @inheritdoc
     */
    public static function parseInputOnUpdate(
        $index,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): array {
        $captures = Validator::onUpdateValidator($index, $container)
            ->assert(static::parseJson($container, $request))
            ->getCaptures();

        return static::prepareCaptures(
            $captures,
            Model::FIELD_ID,
            Validator::captureNames()
        );
    }
}
