<?php

namespace Saaze\Templates;

use Saaze\Interfaces\CollectionInterface;
use Saaze\Interfaces\EntryInterface;
use Saaze\Interfaces\EntryManagerInterface;
use Saaze\Interfaces\TemplateParserInterface;
use Saaze\Interfaces\TemplateManagerInterface;

class TemplateManager implements TemplateManagerInterface
{
    /**
     * @var EntryManagerInterface
     */
    protected $entryManager;

    /**
     * @var TemplateParserInterface
     */
    protected $templateParser;

    public function __construct(EntryManagerInterface $entryManager, TemplateParserInterface $templateParser)
    {
        $this->entryManager   = $entryManager;
        $this->templateParser = $templateParser;
    }

    /**
     * @param CollectionInterface $collection
     * @param int $page
     * @return string
     */
    public function renderCollection(CollectionInterface $collection, $page)
    {
        $this->entryManager->setCollection($collection);

        $template = 'collection';
        if ($this->templateParser->templateExists($collection->slug() . '/index')) {
            $template = $collection->slug() . '/index';
        }

        $page    = filter_var($page, FILTER_SANITIZE_NUMBER_INT);
        $perPage = filter_var(SAAZE_ENTRIES_PER_PAGE, FILTER_SANITIZE_NUMBER_INT);

        return $this->templateParser->render($template, [
            'collection' => $collection->data(),
            'entries'    => $this->entryManager->getEntriesForTemplate($page, $perPage),
        ]);
    }

    /**
     * @param EntryInterface $entry
     * @return string
     */
    public function renderEntry(EntryInterface $entry)
    {
        $this->entryManager->setCollection($entry->getCollection());

        $entryData = $entry->data();
        $template  = 'entry';

        if (!empty($entryData['template']) && $this->templateParser->templateExists($entryData['template'])) {
            $template = $entryData['template'];
        } elseif ($this->templateParser->templateExists($entry->getCollection()->slug() . '/entry')) {
            $template = $entry->getCollection()->slug() . '/entry';
        }

        return $this->templateParser->render($template, [
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
        if ($this->templateParser->templateExists("error{$code}")) {
            $template = "error{$code}";
        }

        if (!$this->templateParser->templateExists($template)) {
            return "{$code} {$message}";
        }

        return $this->templateParser->render($template, [
            'message' => $message,
            'code'    => $code,
        ]);
    }
}
