<?php

namespace Saaze;

use Mimey\MimeTypes;
use Symfony\Component\Routing;
use Saaze\Entries\EntryManager;
use Saaze\Templates\TemplateManager;
use Saaze\Collections\CollectionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

        if ($this->isStaticFile($request->getPathInfo())) {
            $response = $this->handleStaticFile($request->getPathInfo());
            return $response->send();
        }

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
                if ($collection->indexIsEntry()) {
                    $routes->add("{$collection->slug()}_index_entry", new Routing\Route($collection->indexRoute(), ['collection' => $collection->slug(), 'slug' => 'index']));
                } else {
                    $routes->add("{$collection->slug()}_index", new Routing\Route($collection->indexRoute(), ['collection' => $collection->slug()]));
                }

                $routes->add("{$collection->slug()}_page", new Routing\Route($collection->indexRoute() . '/page/{page}', ['collection' => $collection->slug()]));
            }
            if ($collection->entryRoute()) {
                $routes->add("{$collection->slug()}_entry", new Routing\Route($collection->entryRoute(), ['collection' => $collection->slug()]));
            }
        }

        return $routes;
    }

    /**
     * @return bool
     */
    protected function isStaticFile($pathInfo)
    {
        return !is_dir(SAAZE_PUBLIC_PATH . $pathInfo) && file_exists(SAAZE_PUBLIC_PATH . $pathInfo);
    }

    /**
     * @param string $pathInfo
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function handleStaticFile($pathInfo)
    {
        $response = new BinaryFileResponse(SAAZE_PUBLIC_PATH . $pathInfo);

        $ext = pathinfo($pathInfo, PATHINFO_EXTENSION);
        if (!$ext) {
            return $response;
        }

        $mimes    = new MimeTypes();
        $mimeType = $mimes->getMimeType($ext);

        if ($mimeType) {
            $response->headers->set('Content-Type', $mimeType);
        }

        return $response;
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
