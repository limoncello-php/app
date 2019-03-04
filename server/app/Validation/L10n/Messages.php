<?php namespace App\Validation\L10n;

/**
 * @package App
 */
interface Messages extends \Limoncello\Flute\L10n\Messages
{
    /** @var string Validation Message Template */
    const IS_EMAIL = 'The value should be a valid email address.';

    /** @var string Validation Message Template */
    const CONFIRMATION_SHOULD_MATCH_PASSWORD = 'Passwords should match.';
}
