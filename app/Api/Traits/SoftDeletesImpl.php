<?php namespace App\Api\Traits;

use App\Database\Models\Model;
use DateTime;
use Doctrine\DBAL\Query\QueryBuilder;
use Limoncello\JsonApi\Contracts\Models\ModelSchemesInterface;

/**
 * @package App
 *
 * @method int update(int $index, array $attributes)
 * @method ModelSchemesInterface getModelSchemes()
 * @method string getModelClass()
 * @method string buildTableColumn(string $table, string $column)
 */
trait SoftDeletesImpl
{
    /**
     * @param QueryBuilder $builder
     *
     * @return QueryBuilder
     */
    protected function addSoftDeleteCondition(QueryBuilder $builder)
    {
        $tableName  = $this->getModelSchemes()->getTable($this->getModelClass());
        $fullColumn = $this->buildTableColumn($tableName, Model::FIELD_DELETED_AT);
        $builder->andWhere($fullColumn . ' IS NULL');

        return $builder;
    }

    /**
     * @param string|int $index
     *
     * @return int
     */
    protected function softDelete($index)
    {
        return $this->update($index, [Model::FIELD_DELETED_AT => new DateTime()]);
    }
}
