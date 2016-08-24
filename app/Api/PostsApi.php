<?php namespace App\Api;

use App\Api\Traits\SoftDeletes;
use App\Authentication\Contracts\AccountManagerInterface;
use App\Database\Models\Post as Model;
use App\Database\Models\User;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @package App
 */
class PostsApi extends BaseApi
{
    use SoftDeletes;

    const MODEL = Model::class;

    /**
     * @inheritdoc
     */
    public function create(array $attributes, array $toMany = [])
    {
        /** @var AccountManagerInterface $accountManager */
        $accountManager = $this->getContainer()->get(AccountManagerInterface::class);

        // user must be signed-in
        $currentUser = $accountManager->getAccount()->getUser();
        $attributes[Model::FIELD_ID_USER] = $currentUser->{User::FIELD_ID};

        return parent::create($attributes, $toMany);
    }

    /**
     * @inheritdoc
     */
    protected function builderSaveResourceOnUpdate(QueryBuilder $builder)
    {
        return $this->addCurrentUserCondition(parent::builderSaveResourceOnUpdate($builder), Model::FIELD_ID_USER);
    }

    /**
     * @inheritdoc
     */
    protected function builderOnDelete(QueryBuilder $builder)
    {
        return $this->addCurrentUserCondition(parent::builderOnDelete($builder), Model::FIELD_ID_USER);
    }
}
