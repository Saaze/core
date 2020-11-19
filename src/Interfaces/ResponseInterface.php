<?php

namespace Saaze\Interfaces;

interface ResponseInterface
{
    /**
     * Sends HTTP headers and content.
     *
     * @return $this
     */
    public function send();
}
