<?php namespace App\Json\Controllers;

use App\Api\RolesApi as Api;
use App\Json\Schemes\RoleScheme as Scheme;
use App\Json\Validators\Role\RoleCreate;
use App\Json\Validators\Role\RoleUpdate;
use Limoncello\Flute\Contracts\Http\Query\QueryParserInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class RolesController extends BaseController
{
    /** @inheritdoc */
    const API_CLASS = Api::class;

    /** @inheritdoc */
    const SCHEMA_CLASS = Scheme::class;

    /** @inheritdoc */
    const ON_CREATE_VALIDATION_RULES_SET_CLASS = RoleCreate::class;

    /** @inheritdoc */
    const ON_UPDATE_VALIDATION_RULES_SET_CLASS = RoleUpdate::class;

    /**
     * @inheritdoc
     *
     * By default no filters, sorts and includes are allowed (will be ignored). We override this method
     * in order allow it.
     */
    protected static function configureOnIndexParser(QueryParserInterface $parser): QueryParserInterface
    {
        return parent::configureOnIndexParser($parser)
            ->withAllowedFilterFields([
                Scheme::RESOURCE_ID,
                Scheme::ATTR_DESCRIPTION,
            ])
            ->withAllowedSortFields([
                Scheme::RESOURCE_ID,
                Scheme::ATTR_DESCRIPTION,
            ]);
    }
}
