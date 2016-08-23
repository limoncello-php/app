<?php namespace App\Http\Validators;

use App\I18n\ValidationCodes;
use Doctrine\DBAL\Connection;
use Interop\Container\ContainerInterface;
use Limoncello\JsonApi\Contracts\I18n\TranslatorInterface as JsonApiTranslatorInterface;
use Limoncello\JsonApi\Contracts\Models\ModelSchemesInterface;
use Limoncello\JsonApi\Contracts\Schema\JsonSchemesInterface;
use Limoncello\JsonApi\Validation\Validator;
use Limoncello\Validation\Captures\CaptureAggregator;
use Limoncello\Validation\Contracts\RuleInterface;
use Limoncello\Validation\Contracts\TranslatorInterface as ValidationTranslatorInterface;

/**
 * @package App
 */
abstract class BaseValidator extends Validator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $errorStatus      = 422;
        $unlistedAttrRule = $unlistedRelRule = static::fail();
        parent::__construct(
            $container->get(JsonApiTranslatorInterface::class),
            $container->get(ValidationTranslatorInterface::class),
            $container->get(JsonSchemesInterface::class),
            $container->get(ModelSchemesInterface::class),
            $errorStatus,
            $unlistedAttrRule,
            $unlistedRelRule
        );
    }

    /**
     * @param int|string $index
     *
     * @return RuleInterface
     */
    public function idEquals($index)
    {
        return static::equals($index);
    }

    /**
     * @return RuleInterface
     */
    public function absentOrNull()
    {
        return static::isNull();
    }

    /**
     * @param int|null $maxLength
     *
     * @return RuleInterface
     */
    protected function textValue($maxLength = null)
    {
        return static::andX(static::isString(), static::stringLength(null, $maxLength));
    }

    /**
     * @param string $table
     * @param string $primary
     * @param int    $messageCode
     *
     * @return RuleInterface
     */
    protected function dbId($table, $primary, $messageCode = ValidationCodes::DB_EXISTS)
    {
        return static::andX(static::isNumeric(), static::callableX(function ($index) use ($table, $primary) {
            return $this->exists($table, $primary, $index);
        }, $messageCode));
    }

    /**
     * @return RuleInterface
     */
    protected static function isEmail()
    {
        $condition = function ($input) {
            $result = filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
            return $result;
        };

        $messageCode = ValidationCodes::IS_EMAIL;

        return static::andX(static::isString(), static::ifX($condition, static::success(), static::fail($messageCode)));
    }

    /**
     * @return RuleInterface
     */
    protected static function isUrl()
    {
        $condition = function ($input) {
            $result = filter_var($input, FILTER_VALIDATE_URL) !== false;
            return $result;
        };

        $messageCode = ValidationCodes::IS_URL;

        return static::andX(static::isString(), static::ifX($condition, static::success(), static::fail($messageCode)));
    }

    /**
     * @param string $table
     * @param string $primary
     * @param int    $messageCode
     *
     * @return RuleInterface
     */
    protected function isUnique($table, $primary, $messageCode = ValidationCodes::DB_UNIQUE)
    {
        return static::callableX(function ($index) use ($table, $primary) {
            return $this->exists($table, $primary, $index) === false;
        }, $messageCode);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @param string $value
     *
     * @return bool
     */
    protected function exists($tableName, $columnName, $value)
    {
        $fetched = $this->getConnection()
            ->executeQuery("SELECT $columnName FROM $tableName WHERE $columnName = ? LIMIT 1", [$value])
            ->fetch();
        $result = $fetched !== false;

        return $result;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->getContainer()->get(Connection::class);
    }

    /**
     * @inheritdoc
     */
    protected function createIdCaptureAggregator()
    {
        return new CaptureAggregator();
    }

    /**
     * @inheritdoc
     */
    protected function createAttributesAndToOneCaptureAggregator()
    {
        return new CaptureAggregator();
    }

    /**
     * @inheritdoc
     */
    protected function createToManyCaptureAggregator()
    {
        return new CaptureAggregator();
    }
}
