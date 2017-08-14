<?php namespace App\Json\Validators\Rules;

use Limoncello\Flute\Contracts\Validation\ContextInterface;
use Limoncello\Validation\Blocks\ProcedureBlock;
use Limoncello\Validation\Contracts\Blocks\ExecutionBlockInterface;
use Limoncello\Validation\Execution\BlockReplies;
use Limoncello\Validation\Rules\BaseRule;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class IsEmailRule extends BaseRule
{
    /**
     * @inheritdoc
     */
    public function toBlock(): ExecutionBlockInterface
    {
        return (new ProcedureBlock([self::class, 'execute']))->setProperties($this->getStandardProperties());
    }

    /**
     * @param mixed            $value
     * @param ContextInterface $context
     *
     * @return array
     */
    public static function execute($value, ContextInterface $context): array
    {
        $isValidEmail = is_string($value) === true && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;

        return $isValidEmail === true ?
            BlockReplies::createSuccessReply($value) :
            BlockReplies::createErrorReply($context, $value, AppErrorCodes::IS_EMAIL);
    }
}
