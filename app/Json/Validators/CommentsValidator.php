<?php namespace App\Json\Validators;

use App\Data\Models\Comment as Model;
use App\Data\Models\Post;
use App\Json\Schemes\CommentScheme as Scheme;
use App\Json\Validators\CommentsValidator as Himself;
use Limoncello\Validation\Contracts\RuleInterface;
use Psr\Container\ContainerInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
abstract class CommentsValidator extends BaseAppValidator
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
                        Scheme::ATTR_TEXT  => $this->required($this->text()),
                    ],
                    self::RULE_TO_ONE => [
                        Scheme::REL_POST => $this->required($this->postId()),
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
                        Scheme::ATTR_TEXT  => $this->text(),
                    ],
                    // do not allow changing posts for existing comments
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
        return [Model::FIELD_TEXT, Model::FIELD_ID_POST];
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
    protected function postId(): RuleInterface
    {
        return $this->primary(Post::TABLE_NAME, Post::FIELD_ID);
    }
}
