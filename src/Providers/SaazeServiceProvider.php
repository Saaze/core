<?php

namespace Saaze\Providers;

use Saaze\Interfaces\CollectionManagerInterface;
use Saaze\Interfaces\RouteInterface;

class SaazeServiceProvider extends ServiceProvider
{
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
    protected function setUpRoutes()
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
