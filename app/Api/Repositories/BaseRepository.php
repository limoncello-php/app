<?php namespace App\Api\Repositories;

use Limoncello\JsonApi\Adapters\Repository;
use Limoncello\Models\FieldTypes;

/**
 * @package App
 */
class BaseRepository extends Repository
{
    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    protected function getColumns($class)
    {
        $table = $this->getSchemaStorage()->getTable($class);
        foreach ($this->getSchemaStorage()->getAttributeTypes($class) as $column => $type) {
            if ($type === FieldTypes::DATE) {
                yield null => $this->getRawExpressionForDate($table, $column);
            } else {
                yield $table => $column;
            }
        }
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
DATE_FORMAT(CONVERT_TZ(`$table`.`$column`, @@session.time_zone, 'UTC'), '%Y-%m-%dT%H:%i:%s+0000') as `$column`
EOT;
        return $result;
    }
}
