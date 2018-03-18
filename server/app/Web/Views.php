<?php namespace App\Web;

/**
 * @package App\L10n
 */
interface Views
{
    /**
     * Namespace name for mapping template IDs with localized templates.
     */
    const NAMESPACE = 'App.Views.Pages';

    /**
     * Template ID.
     */
    const NOT_FOUND_PAGE = 0;

    /**
     * Template ID.
     */
    const HOME_PAGE = self::NOT_FOUND_PAGE + 1;

    /**
     * Template ID.
     */
    const BOARDS_PAGE = self::HOME_PAGE + 1;

    /**
     * Template ID.
     */
    const BOARD_PAGE = self::BOARDS_PAGE + 1;

    /**
     * Template ID.
     */
    const POST_PAGE = self::BOARD_PAGE + 1;
}
