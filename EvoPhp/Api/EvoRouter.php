<?php 

namespace EvoPhp\Api;

use Inhere\Route\Router;
use Inhere\Route\Dispatcher\Dispatcher;

class EvoRouter
{
    private $request;

    private $router;

    function __construct()
    {
        $this->router = new Router();
        $this->router->config([
            'ignoreLastSlash' => true,
            // 'actionExecutor' => 'run',
            
            // enable autoRoute, work like yii framework
            // you can access '/demo' '/admin/user/info', Don't need to configure any route
            'autoRoute' => 1,
            // 'controllerNamespace' => 'Example\\controllers',
            // 'controllerSuffix' => 'Controller',
        ]);
    }

    function __call($name, $args)
    {
        $name = strtolower($name);
        list($route, $method) = $args;
        $this->router->$name($route, $method);
    }

    function __destruct()
    {

    }

    function dispatch($path = '', $method = '') {
        try {
            $dispatcher = new Dispatcher([
                'dynamicAction' => true,
            ]);
            
            // on notFound, output a message.
            $dispatcher->on(Dispatcher::ON_NOT_FOUND, function ($path) {
               echo "the page $path not found!";
            });
            
            $this->router->dispatch($dispatcher, $path, $method);
        }
        catch(\Exception $exception) {
            ErrorHandler::handleException($exception);
        }
    }
}