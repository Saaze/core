<?php

namespace Saaze\Providers;

use Saaze\Interfaces\CollectionManagerInterface;

class SaazeServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $bindings = [
        \Saaze\Interfaces\EntryInterface::class             => \Saaze\Entries\Entry::class,
        \Saaze\Interfaces\EntryManagerInterface::class      => \Saaze\Entries\EntryManager::class,
        \Saaze\Interfaces\EntryParserInterface::class       => \Saaze\Entries\EntryParser::class,
        \Saaze\Interfaces\CollectionInterface::class        => \Saaze\Collections\Collection::class,
        \Saaze\Interfaces\CollectionManagerInterface::class => \Saaze\Collections\CollectionManager::class,
        \Saaze\Interfaces\CollectionParserInterface::class  => \Saaze\Collections\CollectionParser::class,
        \Saaze\Interfaces\ContentParserInterface::class     => \Saaze\Content\MarkdownContentParser::class,
        \Saaze\Interfaces\RouteInterface::class             => \Saaze\Routing\Route::class,
        \Saaze\Interfaces\RouterInterface::class            => \Saaze\Routing\Router::class,
        \Saaze\Interfaces\TemplateManagerInterface::class   => \Saaze\Templates\TemplateManager::class,
        \Saaze\Interfaces\TemplateParserInterface::class    => \Saaze\Templates\BladeTemplateParser::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->setUpConfig();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setUpRoutes();
    }

    /**
     * @return void
     */
    protected function setUpConfig()
    {
        $this->addConfig([
            'path.base'               => SAAZE_PATH,
            'path.cache'              => SAAZE_PATH . '/' . ($_ENV['CACHE_PATH']     ?? 'cache'),
            'path.content'            => SAAZE_PATH . '/' . ($_ENV['CONTENT_PATH']   ?? 'content'),
            'path.public'             => SAAZE_PATH . '/' . ($_ENV['PUBLIC_PATH']    ?? 'public'),
            'path.templates'          => SAAZE_PATH . '/' . ($_ENV['TEMPLATES_PATH'] ?? 'templates'),
            'config.debug'            => $_ENV['DEBUG'] ?? false,
            'config.entries_per_page' => $_ENV['ENTRIES_PER_PAGE'] ?? 10,
        ]);
    }

    /**
     * @return void
     */
    protected function setUpRoutes()
    {
        $collectionManager = container()->get(CollectionManagerInterface::class);
        $collections       = $collectionManager->getCollections();

        foreach ($collections as $collection) {
            if ($collection->indexRoute()) {
                $this->addRoute($collection->indexRoute(), [\Saaze\Controllers\CollectionController::class, 'index']);
                $this->addRoute($collection->indexRoute() . '/page/{page}', [\Saaze\Controllers\CollectionController::class, 'index']);
            }
            if ($collection->entryRoute()) {
                $this->addRoute($collection->entryRoute(), [\Saaze\Controllers\CollectionController::class, 'entry']);
            }
        }
    }
}
