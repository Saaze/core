<?php

namespace Saaze\Commands\Make;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeEntryCommand extends MakeCommand
{
    protected static $defaultName = 'make:entry';

    protected function configure()
    {
        $this->setDescription('Create a new entry')
             ->addArgument('collection', InputArgument::REQUIRED, 'The id of the collection')
             ->addArgument('id', InputArgument::REQUIRED, 'The id for the entry')
             ->addOption('title', null, InputOption::VALUE_REQUIRED, 'The title for the entry');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collection = $input->getArgument('collection');
        $id         = $input->getArgument('id');
        $title      = $input->getOption('title');

        $collection = $this->sanitizeFilename($collection);
        $id         = $this->sanitizeFilename($id);

        if (!file_exists(content_path() . "/{$collection}.yml")) {
            $output->writeln("<error>The collection \"{$collection}\" does not exist</error>");
            return Command::FAILURE;
        }
        if (file_exists(content_path() . "/{$collection}/{$id}.md")) {
            $output->writeln("<error>The entry \"{$collection}/{$id}\" already exists</error>");
            return Command::FAILURE;
        }
        if (!is_dir(content_path() . "/{$collection}")) {
            mkdir(content_path() . "/{$collection}", 0777, true);
        }

        $data    = ['title' => $title ?: $id];
        $yaml    = Yaml::dump($data);
        $content = "$yaml\n---\n";

        file_put_contents(content_path() . "/{$collection}/{$id}.md", $content);

        $output->writeln("<info>Entry \"{$collection}/{$id}\" successfully created</info>");

        return Command::SUCCESS;
    }
}
