<?php namespace App\Api\Repositories;

use App\Database\Migrations\DateTimeFunctionMigration;
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
        $functionName = DateTimeFunctionMigration::FUNCTION_NAME;
        $result       = "$functionName(`$table`.`$column`) as `$column`";

        return $result;
    }
}
