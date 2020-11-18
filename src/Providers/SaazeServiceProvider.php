<?php

namespace Saaze\Providers;

use Saaze\Interfaces\CollectionManagerInterface;
use Saaze\Interfaces\RouteInterface;

class SaazeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->saazeBindings();
        $this->saazeConfig();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->saazeRoutes();
    }

    /**
     * @return void
     */
    protected function saazeBindings()
    {
        $this->setBindings([
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
        ]);
    }

    /**
     * @return void
     */
    protected function saazeConfig()
    {
        $this->setConfig([
            'path.base'               => SAAZE_PATH,
            'path.cache'              => SAAZE_PATH . '/' . ($_ENV['CACHE_PATH']     ?? 'cache'),
            'path.content'            => SAAZE_PATH . '/' . ($_ENV['CONTENT_PATH']   ?? 'content'),
            'path.public'             => SAAZE_PATH . '/' . ($_ENV['PUBLIC_PATH']    ?? 'public'),
            'path.templates'          => SAAZE_PATH . '/' . ($_ENV['TEMPLATES_PATH'] ?? 'templates'),
            'config.entries_per_page' => $_ENV['ENTRIES_PER_PAGE'] ?? 10,
        ]);
    }

    /**
     * @return void
     */
    protected function saazeRoutes()
    {
        $collectionManager = $this->container->get(CollectionManagerInterface::class);
        $collections       = $collectionManager->getCollections();
        $routes            = [];

        foreach ($collections as $collection) {
            if ($collection->indexRoute()) {
                if ($collection->indexIsEntry()) {
                    $routes[] = $this->container->make(RouteInterface::class, [
                        'path'   => $collection->indexRoute(),
                        'params' => ['collection' => $collection->slug(), 'slug' => 'index'],
                    ]);
                } else {
                    $routes[] = $this->container->make(RouteInterface::class, [
                        'path'   => $collection->indexRoute(),
                        'params' => ['collection' => $collection->slug()],
                    ]);
                }

                $routes[] = $this->container->make(RouteInterface::class, [
                    'path'   => $collection->indexRoute() . '/page/{page}',
                    'params' => ['collection' => $collection->slug()],
                ]);
            }
            if ($collection->entryRoute()) {
                $routes[] = $this->container->make(RouteInterface::class, [
                    'path'         => $collection->entryRoute(),
                    'params'       => ['collection' => $collection->slug()],
                    'requirements' => ['slug' => '.+'],
                ]);
            }
        }

        $this->setRoutes($routes);
    }
}
