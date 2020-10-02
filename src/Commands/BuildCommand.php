<?php

namespace Saaze\Commands;

use Saaze\Entries\Entry;
use Saaze\Entries\EntryManager;
use Saaze\Collections\Collection;
use Saaze\Collections\CollectionManager;
use Saaze\Interfaces\CollectionManagerInterface;
use Saaze\Interfaces\EntryManagerInterface;
use Saaze\Interfaces\TemplateManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    protected static $defaultName = 'build';

    /**
     * @var CollectionManagerInterface
     */
    protected $collectionManager;

    /**
     * @var EntryManagerInterface
     */
    protected $entryManager;

    /**
     * @var TemplateManagerInterface
     */
    protected $templateManager;

    public function __construct(CollectionManagerInterface $collectionManager, EntryManagerInterface $entryManager, TemplateManagerInterface $templateManager)
    {
        parent::__construct();

        $this->collectionManager = $collectionManager;
        $this->entryManager      = $entryManager;
        $this->templateManager   = $templateManager;
    }

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

        $output->writeln("<info>Building static site in {$dest}...</info>");

        $startTime = microtime(true);

        $this->clearBuildDirectory($dest);

        $collections     = $this->collectionManager->getCollections();
        $collectionCount = 0;
        $entryCount      = 0;

        foreach ($collections as $collection) {
            $this->entryManager->setCollection($collection);

            $entries    = $this->entryManager->getEntries();
            $totalPages = ceil(count($entries) / SAAZE_ENTRIES_PER_PAGE);

            if ($this->buildCollectionIndex($collection, null, $dest)) {
                $collectionCount++;
            }

            for ($page = 1; $page <= $totalPages; $page++) {
                $this->buildCollectionIndex($collection, $page, $dest);
            }

            foreach ($entries as $entry) {
                $entry->setCollection($collection);

                if ($this->buildEntry($collection, $entry, $dest)) {
                    $entryCount++;
                }
            }
        }

        $endTime     = microtime(true);
        $elapsedTime = $endTime - $startTime;
        $timeString  = number_format($elapsedTime, 2) . ' secs';
        $memString   = $this->humanSize(memory_get_peak_usage());

        $output->writeln("<info>Finished creating {$collectionCount} collections and {$entryCount} entries ({$timeString} / {$memString})</info>");

        return Command::SUCCESS;
    }

    /**
     * @param string $dest
     * @return void
     */
    private function clearBuildDirectory($dest)
    {
        if (!is_dir($dest)) {
            return;
        }

        $it    = new \RecursiveDirectoryIterator($dest, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dest);
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

    /**
     * @param int $bytes
     * @return string
     */
    private function humanSize($bytes)
    {
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), [0,0,2,2,3][$i]).['B','kB','MB','GB','TB'][$i];
    }
}
