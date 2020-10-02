<?php

namespace Saaze\Templates;

use Saaze\Entries\Entry;
use Jenssegers\Blade\Blade;
use Saaze\Collections\Collection;
use Saaze\Interfaces\EntryManagerInterface;
use Saaze\Interfaces\TemplateManagerInterface;

class TemplateManager implements TemplateManagerInterface
{
    /**
     * @var EntryManagerInterface
     */
    protected $entryManager;

    public function __construct(EntryManagerInterface $entryManager)
    {
        $this->entryManager = $entryManager;
    }

    /**
     * @param Collection $collection
     * @param int $page
     * @return string
     */
    public function renderCollection(Collection $collection, $page)
    {
        $this->entryManager->setCollection($collection);

        $template = 'collection';
        if ($this->templateExists($collection->slug() . DIRECTORY_SEPARATOR . 'index')) {
            $template = $collection->slug() . DIRECTORY_SEPARATOR . 'index';
        }

        $page    = filter_var($page, FILTER_SANITIZE_NUMBER_INT);
        $perPage = filter_var(SAAZE_ENTRIES_PER_PAGE, FILTER_SANITIZE_NUMBER_INT);

        return $this->render($template, [
            'collection' => $collection->data(),
            'entries'    => $this->entryManager->getEntriesForTemplate($page, $perPage),
        ]);
    }

    /**
     * @param Entry $entry
     * @return string
     */
    public function renderEntry(Entry $entry)
    {
        $this->entryManager->setCollection($entry->getCollection());

        $entryData = $entry->data();
        $template  = 'entry';

        if (!empty($entryData['template']) && $this->templateExists($entryData['template'])) {
            $template = $entryData['template'];
        } elseif ($this->templateExists($entry->getCollection()->slug() . DIRECTORY_SEPARATOR . 'entry')) {
            $template = $entry->getCollection()->slug() . DIRECTORY_SEPARATOR . 'entry';
        }

        return $this->render($template, [
            'collection' => $entry->getCollection() ? $entry->getCollection()->data() : null,
            'entry'      => $this->entryManager->getEntryForTemplate($entry),
        ]);
    }

    /**
     * @param string $message
     * @param int $code
     * @return string
     */
    public function renderError($message, $code)
    {
        $template = 'error';
        if ($this->templateExists("error{$code}")) {
            $template = "error{$code}";
        }

        if (!$this->templateExists($template)) {
            return "{$code} {$message}";
        }

        return $this->render($template, [
            'message' => $message,
            'code'    => $code,
        ]);
    }

    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function render($template, $data)
    {
        $blade = new Blade(SAAZE_TEMPLATES_PATH, SAAZE_CACHE_PATH . DIRECTORY_SEPARATOR . 'blade');
        return $blade->render($template, $data);
    }

    /**
     * @param string $template
     * @return boolean
     */
    protected function templateExists($template)
    {
        return file_exists(SAAZE_TEMPLATES_PATH . DIRECTORY_SEPARATOR . "{$template}.blade.php");
    }
}
