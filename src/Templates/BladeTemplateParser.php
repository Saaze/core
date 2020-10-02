<?php

namespace Saaze\Templates;

use Jenssegers\Blade\Blade;
use Saaze\Interfaces\TemplateParserInterface;

class BladeTemplateParser implements TemplateParserInterface
{
    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public function render($template, $data = [])
    {
        if (!is_dir(SAAZE_CACHE_PATH . '/blade')) {
            mkdir(SAAZE_CACHE_PATH . '/blade', 0777, true);
        }

        $blade = new Blade(SAAZE_TEMPLATES_PATH, SAAZE_CACHE_PATH . '/blade');
        return $blade->render($template, $data);
    }

    /**
     * @param string $template
     * @return boolean
     */
    public function templateExists($template)
    {
        return file_exists(SAAZE_TEMPLATES_PATH . "/{$template}.blade.php");
    }
}
