<?php namespace App\Web;

/**
 * @package App\L10n
 */
interface Views
{
    /**
     * Namespace name for mapping template IDs with localized templates.
     *
     * see `server/resources/messages/{LANG}/App.Views.Pages.php`
     */
    const NAMESPACE = 'App.Views.Pages';

    /** Template ID. */
    const NOT_UNAUTHORIZED_PAGE = 0;

    /** Template ID. */
    const NOT_FORBIDDEN_PAGE = self::NOT_UNAUTHORIZED_PAGE + 1;

    /** Template ID. */
    const NOT_FOUND_PAGE = self::NOT_FORBIDDEN_PAGE + 1;

    /** Template ID. */
    const HOME_PAGE = self::NOT_FOUND_PAGE + 1;

    /** Template ID. */
    const SIGN_IN_PAGE = self::HOME_PAGE + 1;

    /** Template ID. */
    const USERS_INDEX_PAGE = self::SIGN_IN_PAGE + 1;

    /** Template ID. */
    const USER_MODIFY_PAGE = self::USERS_INDEX_PAGE + 1;

    /** Template ID. */
    const ROLES_INDEX_PAGE = self::USER_MODIFY_PAGE + 1;

    /** Template ID. */
    const ROLE_MODIFY_PAGE = self::ROLES_INDEX_PAGE + 1;
}
