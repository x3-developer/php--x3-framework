<?php

namespace App;

use Exception;

/**
 * Class Container
 */
class Container
{
    /**
     * @var array
     */
    private array $services = [];

    /**
     * @param $name
     * @param callable $resolver
     * @return void
     */
    public function set($name, callable $resolver): void
    {
        $this->services[$name] = $resolver;
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function get($name): mixed
    {
        if (!isset($this->services[$name])) {
            throw new Exception("Service {$name} not found");
        }

        return $this->services[$name]($this);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
