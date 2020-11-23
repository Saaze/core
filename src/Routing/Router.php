<?php

namespace Saaze\Routing;

use Mimey\MimeTypes;
use Symfony\Component\Routing;
use Saaze\Interfaces\CollectionManagerInterface;
use Saaze\Interfaces\EntryManagerInterface;
use Saaze\Interfaces\RouteInterface;
use Saaze\Interfaces\RouterInterface;
use Saaze\Interfaces\TemplateManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Router implements RouterInterface
{
    /**
     * @var CollectionManagerInterface
     */
    protected $collectionManager;

    /**
     * @var EntryManagerInterface
     */
    protected $entryManager;

    /**
     * @var TemplateManagerInterface
     */
    protected $templateManager;

    public function __construct(CollectionManagerInterface $collectionManager, EntryManagerInterface $entryManager, TemplateManagerInterface $templateManager)
    {
        $this->collectionManager = $collectionManager;
        $this->entryManager      = $entryManager;
        $this->templateManager   = $templateManager;
    }

    /**
     * Handle a request
     *
     * @return void
     */
    public function handle()
    {
        $request = Request::createFromGlobals();

        if ($this->isStaticFile($request->getPathInfo())) {
            $response = $this->handleStaticFile($request->getPathInfo());
            return $response->send();
        }

        $routes = $this->getRoutesFromProviders();

        $context = new Routing\RequestContext();
        $context->fromRequest($request);
        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);

        try {
            $request->attributes->add($matcher->match($request->getPathInfo()));
            $response = $this->handleRoute($request);
        } catch (ResourceNotFoundException $e) {
            $response = new Response($this->templateManager->renderError('Not Found', 404, $e), 404);
        } catch (\Exception $e) {
            $response = new Response($this->templateManager->renderError('Internal Server Error', 500, $e), 500);
        }

        $response->send();
    }

    /**
     * @return Routing\RouteCollection
     */
    protected function getRoutesFromProviders()
    {
        $routeCollection = new Routing\RouteCollection();
        $routes          = container()->get('routes');

        foreach ($routes as $route) {
            if (!is_subclass_of($route, RouteInterface::class)) {
                continue;
            }

            $routerRoute = new Routing\Route($route->path(), $route->params(), $route->requirements());

            $routeCollection->add($route->path(), $routerRoute);
        }

        return $routeCollection;
    }

    /**
     * @return bool
     */
    protected function isStaticFile($pathInfo)
    {
        return !is_dir(public_path() . $pathInfo) && file_exists(public_path() . $pathInfo);
    }

    /**
     * @param string $pathInfo
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function handleStaticFile($pathInfo)
    {
        $response = new BinaryFileResponse(public_path() . $pathInfo);

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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    protected function handleRoute($request)
    {
        container()->set('current_route', $request->attributes->all());

        if ($request->attributes->has('_controller')) {
            $controllerResolver = new ControllerResolver();
            $argumentResolver   = new HttpKernel\Controller\ArgumentResolver();

            $controller = $controllerResolver->getController($request);
            $arguments  = $argumentResolver->getArguments($request, $controller);

            return call_user_func_array($controller, $arguments);
        }

        if (!$request->attributes->has('collection')) {
            throw new ResourceNotFoundException('Collection not found');
        }

        $collection = $this->collectionManager->getCollection($request->attributes->get('collection'));
        if (!$collection) {
            throw new ResourceNotFoundException('Collection not found');
        }

        if (!$request->attributes->has('slug')) {
            $page = $request->attributes->get('page', 1);
            return new Response($this->templateManager->renderCollection($collection, $page));
        }

        $this->entryManager->setCollection($collection);

        $entry = $this->entryManager->getEntry($request->attributes->get('slug'));
        if (!$entry) {
            throw new ResourceNotFoundException('Entry not found');
        }

        $entry->setCollection($collection);

        return new Response($this->templateManager->renderEntry($entry));
    }
}
