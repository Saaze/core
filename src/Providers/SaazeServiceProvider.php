<?php

namespace Saaze\Providers;

use Illuminate\Support\ServiceProvider;

class SaazeServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        \Saaze\Interfaces\EntryInterface::class      => \Saaze\Entries\Entry::class,
        \Saaze\Interfaces\CollectionInterface::class => \Saaze\Collections\Collection::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [

        \Saaze\Interfaces\EntryManagerInterface::class      => \Saaze\Entries\EntryManager::class,
        \Saaze\Interfaces\EntryParserInterface::class       => \Saaze\Entries\EntryParser::class,
        \Saaze\Interfaces\CollectionManagerInterface::class => \Saaze\Collections\CollectionManager::class,
        \Saaze\Interfaces\CollectionParserInterface::class  => \Saaze\Collections\CollectionParser::class,
        \Saaze\Interfaces\ContentParserInterface::class     => \Saaze\Content\MarkdownContentParser::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->router->group([
            'namespace' => 'Saaze\Http\Controllers',
        ], function ($router) {
            require __DIR__.'/../routes.php';
        });
    }
}
