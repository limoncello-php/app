<?php namespace App\Json\Validators;

use App\Data\Models\Role as Model;
use App\Json\Schemes\RoleScheme as Scheme;
use App\Json\Validators\RolesValidator as Himself;
use Limoncello\Validation\Contracts\RuleInterface;
use Psr\Container\ContainerInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
abstract class RolesValidator extends BaseAppValidator
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
                    self::RULE_INDEX      => $this->required($this->roleId()),
                    self::RULE_ATTRIBUTES => [
                        Scheme::ATTR_DESCRIPTION => $this->required($this->description()),
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
                        Scheme::ATTR_DESCRIPTION => $this->required($this->description()),
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
        return [Model::FIELD_DESCRIPTION];
    }

    /**
     * @return RuleInterface
     */
    protected function description(): RuleInterface
    {
        return $this->isText(Model::getAttributeLengths()[Model::FIELD_DESCRIPTION]);
    }

    /**
     * @return RuleInterface
     */
    protected function roleId(): RuleInterface
    {
        return $this->andX(
            $this->isText(Model::getAttributeLengths()[Model::FIELD_ID]),
            $this->isUnique(Model::TABLE_NAME, Model::FIELD_ID)
        );
    }
}
