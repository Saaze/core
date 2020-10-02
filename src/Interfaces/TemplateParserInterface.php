<?php

namespace Saaze\Interfaces;

interface TemplateParserInterface
{
    /**
     * Render a template
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    public function render($template, $data = []);

    /**
     * Determine if a template exists
     *
     * @param string $template
     * @return boolean
     */
    public function templateExists($template);
}
