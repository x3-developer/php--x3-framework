<?php

namespace App;

use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * Class Kernel
 */
readonly class Kernel
{
    /**
     * @param Container $container
     * @param Router $router
     */
    public function __construct(
        private Container $container,
        private Router    $router
    )
    {
        $this->registerServices();
        $this->registerControllers();
    }

    /**
     * @return void
     */
    private function registerControllers(): void
    {
        $controllerNamespace = 'App\\Controller';
        $controllerPath      = __DIR__ . '/Controller';

        if (!is_dir($controllerPath)) {
            return;
        }

        foreach (scandir($controllerPath) as $file) {
            if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $controllerClass = $controllerNamespace . '\\' . pathinfo($file, PATHINFO_FILENAME);

                try {
                    $reflectionClass = new ReflectionClass($controllerClass);

                    if ($reflectionClass->isInstantiable()) {
                        $constructor = $reflectionClass->getConstructor();
                        $parameters  = $constructor ? $constructor->getParameters() : [];
                        $args        = [];

                        foreach ($parameters as $parameter) {
                            $paramClass = $parameter->getType()?->getName();

                            if ($paramClass && $this->container->has($paramClass)) {
                                $args[] = $this->container->get($paramClass);
                            }
                        }

                        $this->container->set($controllerClass, function () use ($controllerClass, $args) {
                            return new $controllerClass(...$args);
                        });

                        $controller = $this->container->get($controllerClass);
                        $this->router->registerControllerRoutes($controller);
                    }
                } catch (ReflectionException|Exception $e) {
                    error_log("Failed to register controller $controllerClass: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * @return void
     */
    private function registerServices(): void
    {
        $serviceNamespaces = ['App\\Service', 'App\\Manager', 'App\\Repository', 'App\\Model'];

        foreach ($serviceNamespaces as $namespace) {
            $path = __DIR__ . '/' . str_replace('\\', '/', substr($namespace, 4));

            if (!is_dir($path)) {
                continue;
            }

            foreach (scandir($path) as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $serviceClass = $namespace . '\\' . pathinfo($file, PATHINFO_FILENAME);

                    try {
                        $reflectionClass = new ReflectionClass($serviceClass);

                        if ($reflectionClass->isInstantiable()) {
                            $this->container->set($serviceClass, function () use ($serviceClass) {
                                return new $serviceClass();
                            });
                        }
                    } catch (ReflectionException|Exception $e) {
                        error_log("Failed to register service $serviceClass: " . $e->getMessage());
                    }
                }
            }
        }

        $this->container->set(TemplateRenderer::class, function () {
            return new TemplateRenderer();
        });
    }

    /**
     * @param string $url
     * @param string $method
     * @return void
     */
    public function handleRequest(string $url, string $method): void
    {
        try {
            $this->router->dispatch($url, $method, $this->container);
        } catch (Exception $e) {
            error_log("Failed to dispatch route: " . $e->getMessage());
        }
    }
}
