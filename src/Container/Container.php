<?php

namespace Saaze\Container;

use DI\ContainerBuilder;

class Container
{
    /**
     * @var static
     */
    protected static $instance;

    /**
     * @var \DI\Container
     */
    protected $container;

    protected function __construct()
    {
        $definitions = array_merge(
            $this->getInterfaceDefenitions(),
            $this->getCustomDefenitions()
        );

        foreach ($definitions as $key => $value) {
            if (is_string($value)) {
                $definitions[$key] = \DI\autowire($value);
            }
        }

        $definitions = array_merge(
            $definitions,
            $this->getPathDefenitions(),
            $this->getConfigDefenitions()
        );

        $builder = new ContainerBuilder();
        $builder->useAnnotations(false);
        $builder->addDefinitions($definitions);

        $this->container = $builder->build();
    }

    /**
     * @return \DI\Container
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance->container;
    }

    /**
     * @return array
     */
    protected function getInterfaceDefenitions()
    {
        return [
            \Saaze\Interfaces\EntryInterface::class             => \Saaze\Entries\Entry::class,
            \Saaze\Interfaces\EntryManagerInterface::class      => \Saaze\Entries\EntryManager::class,
            \Saaze\Interfaces\EntryParserInterface::class       => \Saaze\Entries\EntryParser::class,
            \Saaze\Interfaces\CollectionInterface::class        => \Saaze\Collections\Collection::class,
            \Saaze\Interfaces\CollectionManagerInterface::class => \Saaze\Collections\CollectionManager::class,
            \Saaze\Interfaces\CollectionParserInterface::class  => \Saaze\Collections\CollectionParser::class,
            \Saaze\Interfaces\ContentParserInterface::class     => \Saaze\Content\MarkdownContentParser::class,
            \Saaze\Interfaces\RouterInterface::class            => \Saaze\Routing\Router::class,
            \Saaze\Interfaces\TemplateManagerInterface::class   => \Saaze\Templates\TemplateManager::class,
            \Saaze\Interfaces\TemplateParserInterface::class    => \Saaze\Templates\BladeTemplateParser::class,
        ];
    }

    /**
     * @return array
     */
    protected function getCustomDefenitions()
    {
        if (!file_exists(SAAZE_PATH . '/definitions.php')) {
            return [];
        }

        $defenitions = require_once(SAAZE_PATH . '/definitions.php');
        if (!is_array($defenitions)) {
            return [];
        }

        return $defenitions;
    }

    /**
     * @return array
     */
    protected function getPathDefenitions()
    {
        if (!defined('SAAZE_PATH')) {
            throw new \Exception('SAAZE_PATH is not defined');
        }

        return [
            'path.base'      => SAAZE_PATH,
            'path.cache'     => SAAZE_PATH . '/' . ($_ENV['CACHE_PATH']     ?? 'cache'),
            'path.content'   => SAAZE_PATH . '/' . ($_ENV['CONTENT_PATH']   ?? 'content'),
            'path.public'    => SAAZE_PATH . '/' . ($_ENV['PUBLIC_PATH']    ?? 'public'),
            'path.templates' => SAAZE_PATH . '/' . ($_ENV['TEMPLATES_PATH'] ?? 'templates'),
        ];
    }

    /**
     * @return array
     */
    protected function getConfigDefenitions()
    {
        return [
            'config.entries_per_page' => $_ENV['ENTRIES_PER_PAGE'] ?? 10,
        ];
    }
}
