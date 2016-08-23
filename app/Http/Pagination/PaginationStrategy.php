<?php namespace App\Http\Pagination;

/**
 * @package App
 */
class PaginationStrategy extends \Limoncello\JsonApi\Adapters\PaginationStrategy
{
    /**
     * @inheritdoc
     */
    public function getParameters($rootClass, $class, $path, $relationshipName)
    {
        // you can customize pagination parameters for your resources here

        return parent::getParameters($rootClass, $class, $path, $relationshipName);
    }
}
