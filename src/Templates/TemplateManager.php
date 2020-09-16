<?php

namespace Saaze\Templates;

use Saaze\Entries\Entry;
use Jenssegers\Blade\Blade;
use Saaze\Collections\Collection;
use Saaze\Entries\EntryManager;

class TemplateManager
{
    /**
     * @param Collection $collection
     * @return string
     */
    public function renderCollection(Collection $collection)
    {
        $entryManager = new EntryManager($collection);

        $template = 'collection';
        if ($this->templateExists($collection->slug() . DIRECTORY_SEPARATOR . 'index')) {
            $template = $collection->slug() . DIRECTORY_SEPARATOR . 'index';
        }

        return $this->render($template, [
            'collection' => $collection->data(),
            'entries'    => $entryManager->getEntriesForTemplate(),
        ]);
    }

    /**
     * @param Entry $entry
     * @return string
     */
    public function renderEntry(Entry $entry)
    {
        $entryManager = new EntryManager($entry->getCollection());
        $entryData = $entry->data();
        $template = 'entry';

        if (!empty($entryData['template']) && $this->templateExists($entryData['template'])) {
            $template = $entryData['template'];
        } elseif ($this->templateExists($entry->getCollection()->slug() . DIRECTORY_SEPARATOR . 'entry')) {
            $template = $entry->getCollection()->slug() . DIRECTORY_SEPARATOR . 'entry';
        }

        return $this->render($template, [
            'collection' => $entry->getCollection() ? $entry->getCollection()->data() : null,
            'entry'      => $entryManager->getEntryForTemplate($entry),
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
    public function render($template, $data)
    {
        $blade = new Blade(SAAZE_TEMPLATES_PATH, SAAZE_CACHE_PATH . DIRECTORY_SEPARATOR . 'blade');
        return $blade->render($template, $data);
    }

    /**
     * @param string $template
     * @return boolean
     */
    public function templateExists($template)
    {
        return file_exists(SAAZE_TEMPLATES_PATH . DIRECTORY_SEPARATOR . "{$template}.blade.php");
    }
}
