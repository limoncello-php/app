<?php namespace Config\Services\Database;

/**
 * @package Config
 */
interface DatabaseInterface
{
    /** Config key */
    const USER_NAME = 0;

    /** Config key */
    const PASSWORD = self::USER_NAME + 1;

    /** Config key */
    const PDO_CONNECTION_STRING = self::PASSWORD + 1;

    /** Config key */
    const PDO_OPTIONS = self::PDO_CONNECTION_STRING + 1;
}
