<?php namespace App\Api\Repositories;

use App\Database\Types\DateTimeType;
use Limoncello\JsonApi\Adapters\Repository;

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
        $type = $this->getModelSchemes()->getAttributeType($class, $column);
        if ($type === DateTimeType::NAME) {
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
