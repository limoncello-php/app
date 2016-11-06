<?php namespace App\Database\Models;

/**
 * @package App
 */
interface ModelInterface
{
    /**
     * @return string
     */
    public static function getTableName();

    /**
     * @return string
     */
    public static function getPrimaryKeyName();

    /**
     * @return array
     */
    public static function getAttributeTypes();

    /**
     * @return array
     */
    public static function getAttributeLengths();

    /**
     * @return array
     */
    public static function getRelationships();
}
