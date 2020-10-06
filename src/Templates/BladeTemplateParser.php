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
        if (!is_dir(cache_path() . '/blade')) {
            mkdir(cache_path() . '/blade', 0777, true);
        }

        $blade = new Blade(templates_path(), cache_path() . '/blade');
        return $blade->render($template, $data);
    }

    /**
     * @param string $template
     * @return boolean
     */
    public function templateExists($template)
    {
        return file_exists(templates_path() . "/{$template}.blade.php");
    }
}
