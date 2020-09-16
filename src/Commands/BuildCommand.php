<?php

namespace Saaze\Commands;

use Saaze\Entries\Entry;
use Saaze\Entries\EntryManager;
use Saaze\Collections\Collection;
use Saaze\Templates\TemplateManager;
use Saaze\Collections\CollectionManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    protected static $defaultName = 'build';

    /**
     * @var TemplateManager
     */
    protected $templateManager;

    protected function configure()
    {
        $this->setDescription('Build a static version of the Saaze site')
             ->addOption('dest', 'd', InputOption::VALUE_REQUIRED, 'The directory to save the build output to', 'build');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dest = $input->getOption('dest');

        if (strpos($dest, '/') !== 0) {
            $dest = SAAZE_BASE_DIR . DIRECTORY_SEPARATOR . $dest;
        }

        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        }
        if (!is_dir($dest)) {
            $output->writeln("<error>Destination is not a valid directory ({$dest})</error>");
            return;
        }

        $output->writeln("<info>Building static site in {$dest}...</info>");

        $this->templateManager = new TemplateManager();
        $collectionManager     = new CollectionManager();
        $collections           = $collectionManager->getCollections();

        foreach ($collections as $collection) {
            $entryManager = new EntryManager($collection);
            $entries      = $entryManager->getEntries();
            $totalPages   = ceil(count($entries) / SAAZE_ENTRIES_PER_PAGE);

            if ($this->buildCollectionIndex($collection, null, $dest)) {
                $output->writeln("Collection index created {$collection->slug()}");
            }

            for ($page = 1; $page <= $totalPages; $page++) {
                if ($this->buildCollectionIndex($collection, $page, $dest)) {
                    $output->writeln("Collection index created {$collection->slug()} (page {$page})");
                }
            }

            foreach ($entries as $entry) {
                $entry->setCollection($collection);

                if ($this->buildEntry($collection, $entry, $dest)) {
                    $output->writeln("Entry created {$entry->slug()}");
                }
            }
        }

        $output->writeln("<info>Finished</info>");

        return Command::SUCCESS;
    }

    /**
     * @param Collection $collection
     * @param int $page
     * @param string $dest
     * @return bool
     */
    private function buildCollectionIndex(Collection $collection, $page, $dest)
    {
        if (!$collection->indexRoute()) {
            return false;
        }

        $collectionDir = $dest;

        if ($collection->indexRoute() !== '/') {
            $collectionDir = $dest . DIRECTORY_SEPARATOR . ltrim($collection->indexRoute(), DIRECTORY_SEPARATOR);
        }

        $collectionDir = rtrim($collectionDir, DIRECTORY_SEPARATOR);

        if ($page) {
            $collectionDir .= DIRECTORY_SEPARATOR . 'page' . DIRECTORY_SEPARATOR . $page;
        }

        if (!is_dir($collectionDir)) {
            mkdir($collectionDir, 0777, true);
        }

        file_put_contents($collectionDir . DIRECTORY_SEPARATOR . 'index.html', $this->templateManager->renderCollection($collection, $page));

        return true;
    }

    /**
     * @param Collection $collection
     * @param Entry $entry
     * @param string $dest
     * @return bool
     */
    private function buildEntry(Collection $collection, Entry $entry, $dest)
    {
        if (!$collection->entryRoute()) {
            return false;
        }

        $entryDir = $dest . DIRECTORY_SEPARATOR . ltrim($collection->entryRoute(), DIRECTORY_SEPARATOR);

        if ($entry->slug() === 'index') {
            $entryDir = str_replace('{slug}', '', $entryDir);
        } else {
            $entryDir = str_replace('{slug}', $entry->slug(), $entryDir);
        }

        $entryDir = rtrim($entryDir, DIRECTORY_SEPARATOR);

        if (!is_dir($entryDir)) {
            mkdir($entryDir, 0777, true);
        }

        file_put_contents($entryDir . DIRECTORY_SEPARATOR . 'index.html', $this->templateManager->renderEntry($entry));

        return true;
    }
}
