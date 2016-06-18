<?php namespace App\Database\Models;

/**
 * @package App
 */
interface ModelInterface
{
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
