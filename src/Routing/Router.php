<?php

namespace Saaze\Routing;

use Saaze\Interfaces\RouterInterface;
use League\Route\Router as LeagueRouter;
use Laminas\Diactoros\ServerRequestFactory;
use Saaze\Interfaces\EntryManagerInterface;
use League\Route\Strategy\ApplicationStrategy;
use Saaze\Interfaces\TemplateManagerInterface;
use Saaze\Interfaces\CollectionManagerInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

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
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );

        $strategy = (new ApplicationStrategy)->setContainer(container());
        $router   = (new LeagueRouter)->setStrategy($strategy);
        $router   = $this->addRoutesFromProviders($router);

        $response = $router->dispatch($request);

        (new SapiEmitter)->emit($response);
    }

    /**
     * @param LeagueRouter $router
     * @return LeagueRouter
     */
    protected function addRoutesFromProviders($router)
    {
        $routes = container()->get('routes');

        foreach ($routes as $route) {
            $router->map($route['method'], $route['path'], $route['handler']);
        }

        return $router;
    }
}
