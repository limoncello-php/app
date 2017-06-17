<?php namespace App\Json\Validators;

use Doctrine\DBAL\Connection;
use Limoncello\Flute\Validation\Validator;
use Limoncello\Validation\Contracts\MessageCodes;
use Limoncello\Validation\Contracts\RuleInterface;

/**
 * @package App
 *
 * Here you can put common validation rules and helpers which are shared between validators.
 *
 */
abstract class BaseAppValidator extends Validator
{
    /**
     * @param int|null $maxLength
     *
     * @return RuleInterface
     */
    protected function isText($maxLength = null): RuleInterface
    {
        return static::andX(static::isString(), static::stringLength(null, $maxLength));
    }

    /**
     * @return RuleInterface
     */
    protected function isEmail(): RuleInterface
    {
        $condition = function ($input) {
            $result = filter_var($input, FILTER_VALIDATE_EMAIL) !== false;

            return $result;
        };

        return $this->toString($this->ifX($condition, $this->success(), $this->fail()));
    }

    /**
     * @param string $table
     * @param string $primary
     * @param int    $messageCode
     *
     * @return RuleInterface
     */
    protected function isUnique($table, $primary, $messageCode = MessageCodes::INVALID_VALUE): RuleInterface
    {
        return $this->callableX(function ($index) use ($table, $primary) {
            return $this->exists($table, $primary, $index) === false;
        }, $messageCode);
    }

    /**
     * @param string $table
     * @param string $primary
     * @param int    $messageCode
     *
     * @return RuleInterface
     */
    protected function doesExist($table, $primary, $messageCode = MessageCodes::INVALID_VALUE): RuleInterface
    {
        return $this->callableX(function ($index) use ($table, $primary) {
            return $this->exists($table, $primary, $index) === true;
        }, $messageCode);
    }

    /**
     * @param string $table
     * @param string $primary
     *
     * @return RuleInterface
     */
    protected function primary($table, $primary): RuleInterface
    {
        return static::andX(static::isString(), static::callableX(function ($index) use ($table, $primary) {
            return $this->exists($table, $primary, $index);
        }));
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @param string $value
     *
     * @return bool
     */
    private function exists($tableName, $columnName, $value): bool
    {
        /** @var Connection $connection */
        $connection = $this->getContainer()->get(Connection::class);

        $query = $connection->createQueryBuilder();
        $query
            ->select($columnName)
            ->from($tableName)
            ->where($columnName . '=' . $query->createPositionalParameter($value))
            ->setMaxResults(1);


        $fetched = $query->execute()->fetch();
        $result  = $fetched !== false;

        return $result;
    }
}
