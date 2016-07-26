<?php namespace App\Http\Middleware;

/**
 * @package App
 */
class AuthenticationRequired extends \App\Authorization\Middleware\AuthenticationRequired
{
    // you don't have to but it's nice to have all app middleware in one place
}
