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

        $entries    = $this->entryManager->getEntriesForTemplate();
        $page       = filter_var($page, FILTER_SANITIZE_NUMBER_INT);
        $perPage    = container()->get('config.entries_per_page');
        $pagination = $this->entryManager->paginateEntriesForTemplate($entries, $page, $perPage);

        return $this->templateParser->render($template, [
            'collection' => $this->toObject($collection->data()),
            'pagination' => $this->toObject($pagination),
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

        $collection    = $entry->getCollection() ? $entry->getCollection()->data() : [];
        $templateEntry = $this->entryManager->getEntryForTemplate($entry);

        return $this->templateParser->render($template, [
            'collection' => $this->toObject($collection),
            'entry'      => $this->toObject($templateEntry),
        ]);
    }

    /**
     * @param string $message
     * @param int $code
     * @param \Exception|null $exception
     * @return string
     */
    public function renderError($message, $code, $exception = null)
    {
        if (container()->get('config.debug') && $exception) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();

            return $whoops->handleException($exception);
        }

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

    /**
     * @param array $array
     * @return array
     */
    protected function toObject($array)
    {
        return json_decode(json_encode($array, JSON_FORCE_OBJECT));
    }
}
