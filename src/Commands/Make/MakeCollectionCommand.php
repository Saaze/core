<?php

namespace Saaze\Commands\Make;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCollectionCommand extends MakeCommand
{
    protected static $defaultName = 'make:collection';

    protected function configure()
    {
        $this->setDescription('Create a new collection')
             ->addArgument('id', InputArgument::REQUIRED, 'The id for the collection')
             ->addOption('title', null, InputOption::VALUE_REQUIRED, 'The title for the collection')
             ->addOption('index-route', null, InputOption::VALUE_REQUIRED, 'The index route for the collection')
             ->addOption('entry-route', null, InputOption::VALUE_REQUIRED, 'The entry route for the collection')
             ->addOption('sort-field', null, InputOption::VALUE_REQUIRED, 'The field to use for sorting entries')
             ->addOption('sort-desc', null, InputOption::VALUE_NONE, 'Sort the entries in descending order');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id         = $input->getArgument('id');
        $title      = $input->getOption('title');
        $indexRoute = $input->getOption('index-route');
        $entryRoute = $input->getOption('entry-route');
        $sortField  = $input->getOption('sort-field');
        $sortDesc   = $input->getOption('sort-desc');

        $id = $this->sanitizeFilename($id);

        if (file_exists(content_path() . "/{$id}.yml")) {
            $output->writeln("<error>The collection \"{$id}\" already exists</error>");
            return Command::FAILURE;
        }

        $data = ['title' => $title ?: $id];

        if ($indexRoute) {
            $data['index_route'] = $indexRoute;
        }
        if ($entryRoute) {
            $data['entry_route'] = $entryRoute;
        }
        if ($sortField) {
            $data['sort'] = [
                'field'     => $sortField,
                'direction' => $sortDesc ? 'desc' : 'asc',
            ];
        }

        $yaml = Yaml::dump($data);
        file_put_contents(content_path() . "/{$id}.yml", $yaml);

        $output->writeln("<info>Collection \"{$id}\" successfully created</info>");

        return Command::SUCCESS;
    }
}
