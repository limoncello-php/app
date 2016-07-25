<?php namespace App\Api\Validation;

use App\Database\Models\Board;
use App\Database\Models\Post;
use App\Database\Models\Role;
use App\Database\Models\User;
use Doctrine\DBAL\Connection;
use Limoncello\JsonApi\Contracts\I18n\TranslatorInterface as JsonApiTranslatorInterface;
use Limoncello\JsonApi\Contracts\Schema\JsonSchemesInterface;
use Limoncello\JsonApi\Validation\Validator as BaseValidator;
use Limoncello\Models\Contracts\ModelSchemesInterface;
use Limoncello\Validation\Contracts\RuleInterface;
use Limoncello\Validation\Contracts\TranslatorInterface as ValidationTranslatorInterface;

/**
 * @package App
 */
class Validator extends BaseValidator
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param JsonApiTranslatorInterface    $jsonApiTranslator
     * @param ValidationTranslatorInterface $validationTranslator
     * @param JsonSchemesInterface          $jsonSchemes
     * @param ModelSchemesInterface         $modelSchemes
     * @param Connection                    $connection
     */
    public function __construct(
        JsonApiTranslatorInterface $jsonApiTranslator,
        ValidationTranslatorInterface $validationTranslator,
        JsonSchemesInterface $jsonSchemes,
        ModelSchemesInterface $modelSchemes,
        Connection $connection
    ) {
        $this->connection = $connection;
        $errorStatus      = 422;
        $unlistedAttrRule = $unlistedRelRule = static::fail();

        parent::__construct(
            $jsonApiTranslator,
            $validationTranslator,
            $jsonSchemes,
            $modelSchemes,
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
    public function requiredText($maxLength = null)
    {
        return static::andX(static::required(), $this->optionalText($maxLength));
    }

    /**
     * @param int|null $maxLength
     *
     * @return RuleInterface
     */
    public function optionalText($maxLength = null)
    {
        return static::andX(static::isString(), static::stringLength(1, $maxLength));
    }

    /**
     * @param int $messageCode
     *
     * @return RuleInterface
     */
    public function requiredPostId($messageCode = Translator::DB_EXISTS)
    {
        return static::andX(static::required(), $this->optionalPostId($messageCode));
    }

    /**
     * @param int $messageCode
     *
     * @return RuleInterface
     */
    public function optionalPostId($messageCode = Translator::DB_EXISTS)
    {
        $exists = function ($index) {
            return $this->exists(Post::TABLE_NAME, Post::FIELD_ID, $index);
        };

        return static::callableX($exists, $messageCode);
    }

    /**
     * @param int $messageCode
     *
     * @return RuleInterface
     */
    public function requiredBoardId($messageCode = Translator::DB_EXISTS)
    {
        return static::andX(static::required(), $this->optionalBoardId($messageCode));
    }

    /**
     * @param int $messageCode
     *
     * @return RuleInterface
     */
    public function optionalBoardId($messageCode = Translator::DB_EXISTS)
    {
        $exists = function ($index) {
            return $this->exists(Board::TABLE_NAME, Board::FIELD_ID, $index);
        };

        return static::callableX($exists, $messageCode);
    }

    /**
     * @param int $messageCode
     *
     * @return RuleInterface
     */
    public function requiredRoleId($messageCode = Translator::DB_EXISTS)
    {
        return static::andX(static::required(), $this->optionalRoleId($messageCode));
    }

    /**
     * @param int $messageCode
     *
     * @return RuleInterface
     */
    public function optionalRoleId($messageCode = Translator::DB_EXISTS)
    {
        $exists = function ($index) {
            return $this->exists(Role::TABLE_NAME, Role::FIELD_ID, $index);
        };

        return static::callableX($exists, $messageCode);
    }

    /**
     * @param int $messageCode
     *
     * @return RuleInterface
     */
    public function requiredBoardTitle($messageCode = Translator::DB_UNIQUE)
    {
        return static::andX(static::required(), $this->optionalBoardTitle($messageCode));
    }

    /**
     * @param int $messageCode
     *
     * @return RuleInterface
     */
    public function optionalBoardTitle($messageCode = Translator::DB_UNIQUE)
    {
        $boardExists = function ($title) {
            return false === $this->exists(Board::TABLE_NAME, Board::FIELD_TITLE, $title);
        };

        $maxLength = Board::getAttributeLengths()[Board::FIELD_TITLE];

        return static::andX(static::optionalText($maxLength), static::callableX($boardExists, $messageCode));
    }

    /**
     * @param int $messageCode
     *
     * @return RuleInterface
     */
    public function requiredRoleName($messageCode = Translator::DB_UNIQUE)
    {
        return static::andX(static::required(), $this->optionalRoleName($messageCode));
    }

    /**
     * @param int $messageCode
     *
     * @return RuleInterface
     */
    public function optionalRoleName($messageCode = Translator::DB_UNIQUE)
    {
        $roleExists = function ($title) {
            return false === $this->exists(Role::TABLE_NAME, Role::FIELD_NAME, $title);
        };

        $maxLength = Role::getAttributeLengths()[Role::FIELD_NAME];

        return static::andX(static::optionalText($maxLength), static::callableX($roleExists, $messageCode));
    }

    /**
     * @return RuleInterface
     */
    public function requiredCommentText()
    {
        return static::andX(static::required(), $this->optionalCommentText());
    }

    /**
     * @return RuleInterface
     */
    public function optionalCommentText()
    {
        return static::optionalText();
    }

    /**
     * @return RuleInterface
     */
    public function requiredPostTitle()
    {
        return static::andX(static::required(), $this->optionalPostTitle());
    }

    /**
     * @return RuleInterface
     */
    public function optionalPostTitle()
    {
        $maxLength = Post::getAttributeLengths()[Post::FIELD_TITLE];

        return static::optionalText($maxLength);
    }

    /**
     * @return RuleInterface
     */
    public function requiredPostText()
    {
        return static::andX(static::required(), $this->optionalPostText());
    }

    /**
     * @return RuleInterface
     */
    public function optionalPostText()
    {
        return static::optionalText();
    }

    /**
     * @return RuleInterface
     */
    public function requiredUserTitle()
    {
        return static::andX(static::required(), $this->optionalUserTitle());
    }

    /**
     * @return RuleInterface
     */
    public function optionalUserTitle()
    {
        $maxLength = User::getAttributeLengths()[User::FIELD_TITLE];

        return static::optionalText($maxLength);
    }

    /**
     * @return RuleInterface
     */
    public function requiredUserFirstName()
    {
        return static::andX(static::required(), $this->optionalUserFirstName());
    }

    /**
     * @return RuleInterface
     */
    public function optionalUserFirstName()
    {
        $maxLength = User::getAttributeLengths()[User::FIELD_FIRST_NAME];

        return static::optionalText($maxLength);
    }

    /**
     * @return RuleInterface
     */
    public function requiredUserLastName()
    {
        return static::andX(static::required(), $this->optionalUserLastName());
    }

    /**
     * @return RuleInterface
     */
    public function optionalUserLastName()
    {
        $maxLength = User::getAttributeLengths()[User::FIELD_LAST_NAME];

        return static::optionalText($maxLength);
    }

    /**
     * @return RuleInterface
     */
    public function requiredUserLanguage()
    {
        return static::andX(static::required(), $this->optionalUserLanguage());
    }

    /**
     * @return RuleInterface
     */
    public function optionalUserLanguage()
    {
        $maxLength = User::getAttributeLengths()[User::FIELD_LANGUAGE];

        return static::optionalText($maxLength);
    }

    /**
     * @return RuleInterface
     */
    public function requiredUserEmail()
    {
        return static::andX(static::required(), $this->optionalUserEmail());
    }

    /**
     * @return RuleInterface
     */
    public function optionalUserEmail()
    {
        $maxLength = User::getAttributeLengths()[User::FIELD_EMAIL];

        return static::andX(static::optionalText($maxLength), static::isEmail());
    }

    /**
     * @return RuleInterface
     */
    public function requiredUserPassword()
    {
        return static::andX(static::required(), $this->optionalUserPassword());
    }

    /**
     * @return RuleInterface
     */
    public function optionalUserPassword()
    {
        return static::optionalText();
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

        $messageCode = Translator::IS_EMAIL;

        return static::andX(static::isString(), static::ifX($condition, static::success(), static::fail($messageCode)));
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
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->connection;
    }
}
