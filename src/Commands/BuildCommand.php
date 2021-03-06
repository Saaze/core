<?php

namespace Saaze\Commands;

use Saaze\Interfaces\CollectionInterface;
use Saaze\Interfaces\CollectionManagerInterface;
use Saaze\Interfaces\EntryInterface;
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
            $dest = base_path() . "/{$dest}";
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
            $totalPages = ceil(count($entries) / container()->get('config.entries_per_page'));

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
     * @param CollectionInterface $collection
     * @param int $page
     * @param string $dest
     * @return bool
     */
    private function buildCollectionIndex(CollectionInterface $collection, $page, $dest)
    {
        if (!$collection->indexRoute()) {
            return false;
        }

        $collectionDir = $dest;

        if ($collection->indexRoute() !== '/') {
            $collectionDir = "{$dest}/" . ltrim($collection->indexRoute(), '/');
        }

        $collectionDir = rtrim($collectionDir, '/');

        if ($page) {
            $collectionDir .= "/page/{$page}";
        }

        if (!is_dir($collectionDir)) {
            mkdir($collectionDir, 0777, true);
        }

        file_put_contents($collectionDir . '/index.html', $this->templateManager->renderCollection($collection, $page));

        return true;
    }

    /**
     * @param CollectionInterface $collection
     * @param EntryInterface $entry
     * @param string $dest
     * @return bool
     */
    private function buildEntry(CollectionInterface $collection, EntryInterface $entry, $dest)
    {
        if (!$collection->entryRoute()) {
            return false;
        }

        $entryDir = "{$dest}/" . ltrim($collection->entryRoute(), '/');
        $entryDir = str_replace('{slug}', $entry->slug(), $entryDir);

        if (substr_compare($entry->slug(), 'index', -strlen('index')) === 0) {
            $entryDir = preg_replace('/index$/', '', $entryDir);
        }

        $entryDir = rtrim($entryDir, '/');

        if (!is_dir($entryDir)) {
            mkdir($entryDir, 0777, true);
        }

        file_put_contents("{$entryDir}/index.html", $this->templateManager->renderEntry($entry));

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
