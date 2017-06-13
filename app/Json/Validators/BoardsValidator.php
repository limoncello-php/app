<?php namespace App\Json\Validators;

use App\Data\Models\Board as Model;
use App\Json\Schemes\BoardScheme as Scheme;
use App\Json\Validators\BoardsValidator as Himself;
use Limoncello\Validation\Contracts\RuleInterface;
use Psr\Container\ContainerInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
abstract class BoardsValidator extends BaseAppValidator
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
                        Scheme::ATTR_TITLE => $this->required($this->title()),
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
                        Scheme::ATTR_TITLE => $this->required($this->title()),
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
        return [Model::FIELD_TITLE];
    }

    /**
     * @return RuleInterface
     */
    protected function title(): RuleInterface
    {
        return $this->isText(Model::getAttributeLengths()[Model::FIELD_TITLE]);
    }
}
