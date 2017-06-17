<?php namespace App\Json\Validators;

use App\Data\Models\Role;
use App\Data\Models\User as Model;
use App\Json\Schemes\UserScheme as Scheme;
use App\Json\Validators\UsersValidator as Himself;
use Limoncello\Validation\Contracts\RuleInterface;
use Psr\Container\ContainerInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
abstract class UsersValidator extends BaseAppValidator
{
    /**
     * @param ContainerInterface $container
     *
     * @return self
     */
    public static function onCreateValidator(ContainerInterface $container): self
    {
        return new class ($container) extends Himself
        {
            /**
             * @inheritdoc
             */
            public function __construct(ContainerInterface $container)
            {
                parent::__construct($container, Scheme::TYPE, [
                    self::RULE_INDEX      => $this->isNull(),
                    self::RULE_ATTRIBUTES => [
                        Scheme::ATTR_FIRST_NAME => $this->required($this->firstName()),
                        Scheme::ATTR_LAST_NAME  => $this->required($this->lastName()),
                        Scheme::ATTR_EMAIL      => $this->required($this->email()),
                        Scheme::V_ATTR_PASSWORD => $this->required($this->password()),
                    ],
                    self::RULE_TO_ONE => [
                        Scheme::REL_ROLE => $this->required($this->roleId()),
                    ],
                ]);
            }
        };
    }

    /**
     * @param string|int         $index
     * @param ContainerInterface $container
     *
     * @return self
     */
    public static function onUpdateValidator($index, ContainerInterface $container): self
    {
        return new class ($container, $index) extends Himself
        {
            /**
             * @inheritdoc
             */
            public function __construct(ContainerInterface $container, $index)
            {
                parent::__construct($container, Scheme::TYPE, [
                    self::RULE_INDEX      => $this->equals($index),
                    self::RULE_ATTRIBUTES => [
                        Scheme::ATTR_FIRST_NAME => $this->firstName(),
                        Scheme::ATTR_LAST_NAME  => $this->lastName(),
                        Scheme::ATTR_EMAIL      => $this->email(),
                        Scheme::V_ATTR_PASSWORD => $this->password(),
                    ],
                    self::RULE_TO_ONE => [
                        Scheme::REL_ROLE => $this->roleId(),
                    ],
                ]);
            }
        };
    }

    /**
     * @return string[]
     */
    public static function captureNames(): array
    {
        return [
            Model::FIELD_FIRST_NAME,
            Model::FIELD_LAST_NAME,
            Model::FIELD_EMAIL,
            Model::FIELD_ID_ROLE,
            Scheme::CAPTURE_NAME_PASSWORD
        ];
    }

    /**
     * @return RuleInterface
     */
    protected function firstName(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_FIRST_NAME];

        return $this->toString($this->stringLength(1, $maxLength));
    }

    /**
     * @return RuleInterface
     */
    protected function lastName(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_LAST_NAME];

        return $this->toString($this->stringLength(1, $maxLength));
    }

    /**
     * @return RuleInterface
     */
    protected function email(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_EMAIL];
        $isEmail   = $this->andX(
            $this->toString($this->stringLength(5, $maxLength)),
            $this->isEmail()
        );

        return $this->andX($isEmail, $this->isUnique(Model::TABLE_NAME, Model::FIELD_EMAIL));
    }

    /**
     * @return RuleInterface
     */
    protected function password(): RuleInterface
    {
        return $this->toString($this->stringLength(Model::MIN_PASSWORD_LENGTH));
    }

    /**
     * @return RuleInterface
     */
    protected function roleId(): RuleInterface
    {
        return $this->primary(Role::TABLE_NAME, Role::FIELD_ID);
    }
}
