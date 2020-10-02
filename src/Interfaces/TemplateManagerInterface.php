<?php

namespace Saaze\Interfaces;

interface TemplateManagerInterface
{
    /**
     * Render a collection template
     *
     * @param \Saaze\Collections\Collection $collection
     * @param int $page
     * @return string
     */
    public function renderCollection(\Saaze\Collections\Collection $collection, $page);

    /**
     * Render an entry template
     *
     * @param \Saaze\Entries\Entry $entry
     * @return string
     */
    public function renderEntry(\Saaze\Entries\Entry $entry);

    /**
     * Render an error template
     *
     * @param string $message
     * @param int $code
     * @return string
     */
    public function renderError($message, $code);
}
