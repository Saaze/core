<?php

namespace Saaze;

use Symfony\Component\Routing;
use Saaze\Collections\CollectionManager;
use Saaze\Entries\EntryManager;
use Saaze\Templates\TemplateManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Saaze
{
    /**
     * @var \Saaze\Collections\CollectionManager
     */
    protected $collectionManager;

    /**
     * @var \Saaze\Templates\TemplateManager
     */
    protected $templateManager;

    public function __construct()
    {
        $this->collectionManager = new CollectionManager();
        $this->templateManager   = new TemplateManager();
    }

    public function run()
    {
        $request = Request::createFromGlobals();
        $routes = new Routing\RouteCollection();
        $routes->addCollection($this->getCollectionRoutes());

        $context = new Routing\RequestContext();
        $context->fromRequest($request);
        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);

        try {
            $route    = $matcher->match($request->getPathInfo());
            $response = $this->handleRoute($route);
        } catch (ResourceNotFoundException $e) {
            $response = new Response($this->templateManager->renderError('Not Found', 404), 404);
        } catch (\Exception $e) {
            $response = new Response($this->templateManager->renderError('Internal Server Error', 500), 500);
        }

        $response->send();
    }

    /**
     * @return Routing\RouteCollection
     */
    protected function getCollectionRoutes()
    {
        $routes = new Routing\RouteCollection();
        $collections = $this->collectionManager->getCollections();

        foreach ($collections as $collection) {
            if ($collection->indexRoute()) {
                $routes->add("{$collection->slug()}_index", new Routing\Route($collection->indexRoute(), ['collection' => $collection->slug()]));
                $routes->add("{$collection->slug()}_page", new Routing\Route($collection->indexRoute() . '/page/{page}', ['collection' => $collection->slug()]));
            }
            if ($collection->entryRoute()) {
                $routes->add("{$collection->slug()}_entry", new Routing\Route($collection->entryRoute(), ['collection' => $collection->slug()]));
            }
            if ($collection->entryRoute() === '/{slug}') {
                $routes->add("{$collection->slug()}_entry_index", new Routing\Route('/', ['collection' => $collection->slug(), 'slug' => 'index']));
            }
        }

        return $routes;
    }

    /**
     * @param array $route
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    protected function handleRoute($route)
    {
        if (empty($route['collection'])) {
            throw new ResourceNotFoundException('Collection not found');
        }

        $collection = $this->collectionManager->getCollection($route['collection']);
        if (!$collection) {
            throw new ResourceNotFoundException('Collection not found');
        }

        if (empty($route['slug'])) {
            $page = $route['page'] ?? 1;
            return new Response($this->templateManager->renderCollection($collection, $page));
        }

        $entryManager = new EntryManager($collection);
        $entry = $entryManager->getEntry($route['slug']);
        if (!$entry) {
            throw new ResourceNotFoundException('Entry not found');
        }

        $entry->setCollection($collection);

        return new Response($this->templateManager->renderEntry($entry));
    }
}
