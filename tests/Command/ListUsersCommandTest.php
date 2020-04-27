<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group   Unit
 *
 * @see     https://symfony.com/doc/current/console/commands_as_services.html
 * @see     https://symfony.com/doc/current/console.html#testing-commands
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ListUsersCommandTest extends KernelTestCase
{
    /** @var Command */
    private $command;

    /** @var CommandTester */
    private $commandTester;

    protected function setUp(): void
    {
        $application = new Application(static::createKernel());

        $this->command = $application->find('app:list-users');
        $this->commandTester = new CommandTester($this->command);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->command = null;
        $this->commandTester = null;
    }

    public function testExecuteMethodReturningAZeroStatus(): void
    {
        $this->commandTester->execute(['command' => $this->command->getName()]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testExecuteMethodReturningGoodOutputHeaderAndColumns(): void
    {
        $this->commandTester->execute(['command' => $this->command->getName()]);
        $standardOutput = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Current Users present in DB:', $standardOutput);
        $this->assertStringContainsString('ID', $standardOutput);
        $this->assertStringContainsString('Username', $standardOutput);
        $this->assertStringContainsString('Roles', $standardOutput);
    }

    /**
     * Tested with the standard output and its line numbers.
     */
    public function testExecuteMethodWithMaxResultsLimitedToFiveUsers(): void
    {
        $this->commandTester->execute(['command' => $this->command->getName(), '--max-results' => 5]);
        // Eight lines composed of headers and one ending line + six user rows
        $this->assertSame(14, substr_count($this->commandTester->getDisplay(), "\n"));
    }
}
