<?php

namespace Saaze;

use Laravel\Lumen\Application;

abstract class Saaze
{
    /**
     * @var [type]
     */
    protected $app;

    /**
     * @param string $saazePath
     */
    public function __construct($saazePath)
    {
        define('SAAZE_PATH', $saazePath);
    }

    /**
     * Bootstrap Lumen
     *
     * @return void
     */
    protected function bootstrap()
    {
        (new \Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
            SAAZE_PATH
        ))->bootstrap();

        date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

        /*
        |--------------------------------------------------------------------------
        | Create The Application
        |--------------------------------------------------------------------------
        |
        | Here we will load the environment and create the application instance
        | that serves as the central piece of this framework. We'll use this
        | application as an "IoC" container and router for this framework.
        |
        */

        $this->app = new Application(SAAZE_PATH);

        // $app->withFacades();

        // $app->withEloquent();

        /*
        |--------------------------------------------------------------------------
        | Register Container Bindings
        |--------------------------------------------------------------------------
        |
        | Now we will register a few bindings in the service container. We will
        | register the exception handler and the console kernel. You may add
        | your own bindings here if you like or you can make another file.
        |
        */

        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Saaze\Exceptions\Handler::class
        );

        $this->app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \Saaze\Console\Kernel::class
        );

        /*
        |--------------------------------------------------------------------------
        | Register Config Files
        |--------------------------------------------------------------------------
        |
        | Now we will register the "app" configuration file. If the file exists in
        | your configuration directory it will be loaded; otherwise, we'll load
        | the default version. You may register other files below as needed.
        |
        */

        $this->app->configure('app');

        /*
        |--------------------------------------------------------------------------
        | Register Middleware
        |--------------------------------------------------------------------------
        |
        | Next, we will register the middleware with the application. These can
        | be global middleware that run before and after each request into a
        | route or middleware that'll be assigned to some specific routes.
        |
        */

        // $app->middleware([
        //     App\Http\Middleware\ExampleMiddleware::class
        // ]);

        // $app->routeMiddleware([
        //     'auth' => App\Http\Middleware\Authenticate::class,
        // ]);

        /*
        |--------------------------------------------------------------------------
        | Register Service Providers
        |--------------------------------------------------------------------------
        |
        | Here we will register all of the application's service providers which
        | are used to bind services into the container. Service providers are
        | totally optional, so you are not required to uncomment this line.
        |
        */

        $this->app->register(\Saaze\Providers\SaazeServiceProvider::class);
    }

    /**
     * @return void
     */
    abstract public function run();
}
