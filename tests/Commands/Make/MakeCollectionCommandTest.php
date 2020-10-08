<?php

namespace Saaze\Tests\Commands\Make;

use Saaze\Tests\TestCase;
use Saaze\Commands\Make\MakeCollectionCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

class MakeCollectionCommandTest extends TestCase
{
    /**
     * @var MakeCollectionCommand
     */
    protected $command;

    /**
     * @var string
     */
    protected $collectionPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->command        = container()->get(MakeCollectionCommand::class);
        $this->collectionPath = content_path() . '/test.yml';
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if (file_exists($this->collectionPath)) {
            unlink($this->collectionPath);
        }
    }

    public function testMakeCollectionCommand()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'id' => 'test',
        ]);

        $this->assertFileExists($this->collectionPath);

        $data = Yaml::parseFile($this->collectionPath);

        $this->assertIsArray($data);
        $this->assertEquals('test', $data['title']);
    }

    public function testMakeCollectionCommandCollectionAlreadyExists()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'id' => 'pages',
        ]);

        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testMakeCollectionCommandWithOptions()
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'id'            => 'test',
            '--title'       => 'Test Collection',
            '--index-route' => '/test',
            '--entry-route' => '/test/{slug}',
            '--sort-field'  => 'date',
            '--sort-desc'   => null,
        ]);

        $this->assertFileExists($this->collectionPath);

        $data = Yaml::parseFile($this->collectionPath);

        $this->assertIsArray($data);
        $this->assertEquals('Test Collection', $data['title']);
        $this->assertEquals('/test', $data['index_route']);
        $this->assertEquals('/test/{slug}', $data['entry_route']);
        $this->assertEquals('date', $data['sort']['field']);
        $this->assertEquals('desc', $data['sort']['direction']);
    }
}
