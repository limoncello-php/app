<?php namespace App\Data\Seeds;

use App\Data\Models\RoleScope;
use Doctrine\DBAL\DBALException;
use Exception;
use Limoncello\Contracts\Data\SeedInterface;
use Limoncello\Contracts\Settings\SettingsProviderInterface;
use Limoncello\Data\Seeds\SeedTrait;
use Limoncello\Passport\Adaptors\Generic\Client;
use Limoncello\Passport\Adaptors\Generic\Scope;
use Limoncello\Passport\Contracts\PassportServerIntegrationInterface;
use Limoncello\Passport\Traits\PassportSeedTrait;
use Settings\Passport as S;

/**
 * @package App
 */
class PassportSeed implements SeedInterface
{
    use SeedTrait, PassportSeedTrait;

    /** Scope ID */
    const SCOPE_ADMIN_OAUTH = 'manage_oauth';

    /** Scope ID */
    const SCOPE_ADMIN_USERS = 'manage_users';

    /** Scope ID */
    const SCOPE_ADMIN_ROLES = 'manage_roles';

    /** Scope ID */
    const SCOPE_VIEW_USERS = 'view_users';

    /** Scope ID */
    const SCOPE_VIEW_ROLES = 'view_roles';

    /**
     * @inheritdoc
     *
     * @throws DBALException
     */
    public function run(): void
    {
        /** @var PassportServerIntegrationInterface $integration */
        $container   = $this->getContainer();
        $settings    = $container->get(SettingsProviderInterface::class)->get(S::class);
        $integration = $container->get(PassportServerIntegrationInterface::class);

        // create OAuth scopes

        // scope ID => description (don't hesitate to add required for your application)
        $scopes    = [
            static::SCOPE_ADMIN_OAUTH    => 'Can create, update and delete OAuth clients, redirect URIs and scopes.',
            static::SCOPE_ADMIN_USERS    => 'Can create, update and delete users.',
            static::SCOPE_ADMIN_ROLES    => 'Can create, update and delete roles.',
            static::SCOPE_VIEW_USERS     => 'Can view users.',
            static::SCOPE_VIEW_ROLES     => 'Can view roles.',
        ];
        $scopeRepo = $integration->getScopeRepository();
        foreach ($scopes as $scopeId => $scopeDescription) {
            $scopeRepo->create(
                (new Scope())
                    ->setIdentifier($scopeId)
                    ->setDescription($scopeDescription)
            );
        }

        // create OAuth clients

        $client = (new Client())
            ->setIdentifier($settings[S::KEY_DEFAULT_CLIENT_ID])
            ->setName($settings[S::KEY_DEFAULT_CLIENT_NAME])
            ->setPublic()
            ->useDefaultScopesOnEmptyRequest()
            ->disableScopeExcess()
            ->enablePasswordGrant()
            ->disableCodeGrant()
            ->disableImplicitGrant()
            ->disableClientGrant()
            ->enableRefreshGrant()
            ->setScopeIdentifiers(array_keys($scopes));

        $this->seedClient($integration, $client, [], $settings[S::KEY_DEFAULT_CLIENT_REDIRECT_URIS] ?? []);

        // assign scopes to roles

        $this->assignScopes(RolesSeed::ROLE_ADMIN, [
            static::SCOPE_ADMIN_OAUTH,
            static::SCOPE_ADMIN_USERS,
            static::SCOPE_ADMIN_ROLES,
            static::SCOPE_VIEW_USERS,
            static::SCOPE_VIEW_ROLES,
        ]);

        $this->assignScopes(RolesSeed::ROLE_MODERATOR, [
            static::SCOPE_ADMIN_USERS,
            static::SCOPE_VIEW_USERS,
            static::SCOPE_VIEW_ROLES,
        ]);

        $this->assignScopes(RolesSeed::ROLE_USER, [
            static::SCOPE_VIEW_USERS,
        ]);
    }

    /**
     * @param string   $roleId
     * @param string[] $scopeIds
     *
     * @return void
     *
     * @throws DBALException
     * @throws Exception
     */
    private function assignScopes(string $roleId, array $scopeIds)
    {
        $now = $this->now();
        foreach ($scopeIds as $scopeId) {
            $this->seedRowData(RoleScope::TABLE_NAME, [
                RoleScope::FIELD_ID_ROLE    => $roleId,
                RoleScope::FIELD_ID_SCOPE   => $scopeId,
                RoleScope::FIELD_CREATED_AT => $now,
            ]);
        }
    }
}
