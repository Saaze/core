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
        $definitions = [
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

        $definitions = array_merge($definitions, $this->getCustomDefenitions());

        foreach ($definitions as $key => $value) {
            if (is_string($value)) {
                $definitions[$key] = \DI\autowire($value);
            }
        }

        $builder = new ContainerBuilder();
        $builder->useAnnotations(false);
        $builder->addDefinitions($definitions);

        $this->container = $builder->build();
    }

    /**
     * @return array
     */
    protected function getCustomDefenitions()
    {
        if (!file_exists(SAAZE_BASE_DIR . '/definitions.php')) {
            return [];
        }

        $defenitions = require_once(SAAZE_BASE_DIR . '/definitions.php');
        if (!is_array($defenitions)) {
            return [];
        }

        return $defenitions;
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
}
