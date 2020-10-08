<?php

namespace Saaze\Tests\Commands\Make;

use Saaze\Tests\TestCase;
use Saaze\Commands\Make\MakeEntryCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

class MakeEntryCommandTest extends TestCase
{
    /**
     * @var MakeEntryCommand
     */
    protected $command;

    /**
     * @var string
     */
    protected $entryPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->command   = container()->get(MakeEntryCommand::class);
        $this->entryPath = content_path() . '/pages/test.md';
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if (file_exists($this->entryPath)) {
            unlink($this->entryPath);
        }
    }

    public function testMakeEntryCommand()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'collection' => 'pages',
            'id'         => 'test',
        ]);

        $this->assertFileExists($this->entryPath);
    }

    public function testMakeEntryCommandCollectionDoesntExists()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'collection' => 'nonexistent',
            'id'         => 'test',
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testMakeEntryCommandEntryAlreadyExists()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'collection' => 'pages',
            'id'         => 'index',
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());
    }
}
