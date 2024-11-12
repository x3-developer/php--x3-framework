<?php

namespace App;

use Exception;
use ReflectionClass;

/**
 * Class Router
 */
class Router
{
    /**
     * @var array
     */
    private array $routes = [];

    /**
     * @param object $controller
     * @return void
     */
    public function registerControllerRoutes(object $controller): void
    {
        $reflectionController = new ReflectionClass($controller);

        foreach ($reflectionController->getMethods() as $method) {
            $attributes = $method->getAttributes(Route::class);
            foreach ($attributes as $attribute) {
                /** @var Route $route */
                $route = $attribute->newInstance();
                $this->addRoute($route->path, $controller::class, $method->getName(), $route->methods);
            }
        }
    }

    /**
     * @param string $path
     * @param string $controller
     * @param string $method
     * @param array $httpMethods
     * @return void
     */
    public function addRoute(string $path, string $controller, string $method, array $httpMethods = ['GET']): void
    {
        foreach ($httpMethods as $httpMethod) {
            $this->routes[$httpMethod][$path] = [
                'controller' => $controller,
                'method'     => $method,
            ];
        }
    }

    /**
     * @param string $uri
     * @param string $httpMethod
     * @param Container $container
     * @return mixed
     * @throws Exception
     */
    public function dispatch(string $uri, string $httpMethod, Container $container): mixed
    {
        if (!isset($this->routes[$httpMethod][$uri])) {
            throw new Exception("Route not found: " . $uri);
        }

        $route      = $this->routes[$httpMethod][$uri];
        $controller = $container->get($route['controller']);
        $method     = $route['method'];

        return $controller->$method();
    }
}
