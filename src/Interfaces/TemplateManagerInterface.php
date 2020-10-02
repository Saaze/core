<?php

namespace Saaze\Interfaces;

interface TemplateManagerInterface
{
    /**
     * Render a collection template
     *
     * @param \Saaze\Interfaces\CollectionInterface $collection
     * @param int $page
     * @return string
     */
    public function renderCollection(\Saaze\Interfaces\CollectionInterface $collection, $page);

    /**
     * Render an entry template
     *
     * @param \Saaze\Interfaces\EntryInterface $entry
     * @return string
     */
    public function renderEntry(\Saaze\Interfaces\EntryInterface $entry);

    /**
     * Render an error template
     *
     * @param string $message
     * @param int $code
     * @return string
     */
    public function renderError($message, $code);
}
