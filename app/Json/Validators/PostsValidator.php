<?php namespace App\Json\Validators;

use App\Data\Models\Board;
use App\Data\Models\Post as Model;
use App\Json\Schemes\PostScheme as Scheme;
use App\Json\Validators\PostsValidator as Himself;
use Limoncello\Validation\Contracts\RuleInterface;
use Psr\Container\ContainerInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
abstract class PostsValidator extends BaseAppValidator
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
                        Scheme::ATTR_TEXT  => $this->required($this->text()),
                    ],
                    self::RULE_TO_ONE => [
                        Scheme::REL_BOARD => $this->required($this->boardId()),
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
                        Scheme::ATTR_TITLE => $this->title(),
                        Scheme::ATTR_TEXT  => $this->text(),
                    ],
                    // do not allow changing boards for existing posts
                    self::RULE_TO_ONE => [
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
        return [Model::FIELD_TITLE, Model::FIELD_TEXT, Model::FIELD_ID_BOARD];
    }

    /**
     * @return RuleInterface
     */
    protected function title(): RuleInterface
    {
        return $this->isText(Model::getAttributeLengths()[Model::FIELD_TITLE]);
    }

    /**
     * @return RuleInterface
     */
    protected function text(): RuleInterface
    {
        return $this->isText();
    }

    /**
     * @return RuleInterface
     */
    protected function boardId(): RuleInterface
    {
        return $this->primary(Board::TABLE_NAME, Board::FIELD_ID);
    }
}
