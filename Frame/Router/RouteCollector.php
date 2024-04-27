<?php declare(strict_types=1);

namespace Frame\Router;

use Frame\Router;

class RouteCollector extends Router
{
    private string $groupPrefix = '';
    private array $groupMiddleware = [];
    private int $lastIndex;

    public function get(string $path, mixed $handler, array $middleware = []): self
    {
        $this->addRoute('GET', $this->groupPrefix . $path, $handler, array_merge($middleware, $this->groupMiddleware));
        $this->lastIndex = array_key_last($this->routes);
        return $this;
    }

    public function post(string $path, mixed $handler, array $middleware = []): self
    {
        $this->addRoute('POST', $this->groupPrefix . $path, $handler, array_merge($middleware, $this->groupMiddleware));
        $this->lastIndex = array_key_last($this->routes);
        return $this;
    }

    public function put(string $path, mixed $handler, array $middleware = []): self
    {
        $this->addRoute('PUT', $this->groupPrefix . $path, $handler, array_merge($middleware, $this->groupMiddleware));
        $this->lastIndex = array_key_last($this->routes);
        return $this;
    }

    public function patch(string $path, mixed $handler, array $middleware = []): self
    {
        $this->addRoute('PATCH', $this->groupPrefix . $path, $handler, array_merge($middleware, $this->groupMiddleware));
        $this->lastIndex = array_key_last($this->routes);
        return $this;
    }

    public function delete(string $path, mixed $handler, array $middleware = []): self
    {
        $this->addRoute('DELETE', $this->groupPrefix . $path, $handler, array_merge($middleware, $this->groupMiddleware));
        $this->lastIndex = array_key_last($this->routes);
        return $this;
    }

    public function name(string $name): void
    {
        $this->routes[$name] = $this->routes[$this->lastIndex];
        unset($this->routes[$this->lastIndex]);
    }

    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $previousGroupPrefix = $this->groupPrefix;
        $previousGroupMiddleware = $this->groupMiddleware;

        $this->groupPrefix = $previousGroupPrefix . $prefix;
        $this->groupMiddleware = array_merge($middleware, $previousGroupMiddleware);

        $callback($this);

        $this->groupPrefix = $previousGroupPrefix;
        $this->groupMiddleware = $previousGroupMiddleware;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

}