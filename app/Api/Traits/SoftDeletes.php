<?php namespace App\Api\Traits;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package App
 */
trait SoftDeletes
{
    use SoftDeletesImpl;

    /**
     * @param string|int $index
     *
     * @return int
     */
    public function delete($index)
    {
        return $this->softDelete($index);
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return QueryBuilder
     */
    protected function builderOnIndex(QueryBuilder $builder)
    {
        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->addSoftDeleteCondition(parent::builderOnIndex($builder));
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return QueryBuilder
     */
    protected function builderOnRead(QueryBuilder $builder)
    {
        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->addSoftDeleteCondition(parent::builderOnRead($builder));
    }
}
