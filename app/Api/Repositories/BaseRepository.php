<?php namespace App\Api\Repositories;

use Limoncello\JsonApi\Adapters\Repository;
use Limoncello\Models\FieldTypes;

/**
 * @package App
 */
class BaseRepository extends Repository
{
    /**
     * @inheritdoc
     */
    protected function getColumn($class, $table, $column)
    {
        if ($this->getModelSchemes()->getAttributeType($class, $column) === FieldTypes::DATE) {
            return $this->getRawExpressionForDate($table, $column);
        }

        return parent::getColumn($class, $table, $column);
    }

    /**
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    private function getRawExpressionForDate($table, $column)
    {
        $result = <<<EOT
DATE_FORMAT(CONVERT_TZ(`$table`.`$column`, @@session.time_zone, '+00:00'), '%Y-%m-%dT%H:%i:%s+0000') as `$column`
EOT;
        return $result;
    }
}
