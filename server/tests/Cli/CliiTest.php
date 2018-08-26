<?php namespace Tests\Cli;

use Exception;
use Limoncello\Application\Commands\DataCommand;
use Limoncello\Commands\ExecuteCommandTrait;
use Limoncello\Testing\CommandsDebugIo;
use Tests\TestCase;

/**
 * @package Tests
 */
class CliiTest extends TestCase
{
    use ExecuteCommandTrait;

    /**
     * Demo how you can test and debug console commands.
     *
     * In this example one of the standard command is executed,
     * however custom commands could be tested the same way.
     *
     * @throws Exception
     */
    public function testCommand(): void
    {
        $this->setPreventCommits();

        $arguments = [DataCommand::ARG_ACTION => DataCommand::ACTION_SEED];
        $options   = [];
        $ioMock    = new CommandsDebugIo($arguments, $options);

        $container = $this->createApplication()->createContainer();

        $this->executeCommand([DataCommand::class, DataCommand::COMMAND_METHOD_NAME], $ioMock, $container);

        $this->assertEmpty($ioMock->getErrorRecords());
        $this->assertEmpty($ioMock->getWarningRecords());
        $this->assertNotEmpty($ioMock->getInfoRecords());
    }
}
