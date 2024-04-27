<?php declare(strict_types=1);

namespace Frame;

/*
 * Route patterns examples:
 * Named placeholders in curly braces '/user/{id}'
 * Named placeholders with regular expression '/user/{id:[0-9]+}'
 * Optional segments wrapped in square brackets '/users[/page-{id}]'
 * */
class Router
{
    protected array $routes = [];

    public function addRoute(string $method, string $path, mixed $handler, array $middleware = []): void
    {
        $this->routes[] = compact('method', 'path', 'handler', 'middleware');
    }

    public function matchRoute(string $requestMethod, string $url): array|false
    {
        foreach ($this->routes as ['method' => $method, 'path' => $path, 'handler' => $handler, 'middleware' => $middleware]) {
            if($requestMethod === $method) {
                $pattern = str_replace('/', '\/', $path);
                $pattern = preg_replace('/\[(?![^{]*})/', '(?:', $pattern);
                $pattern = preg_replace('/](?![^{]*})/', ')?', $pattern);
                $pattern = preg_replace('/{(\w+)(:([^}]+))?}/', '(?<$1>$3)', $pattern);
                $pattern = '/^' . $pattern . '$/';

                if (preg_match($pattern, $url, $matches)) {
                    $args = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    return compact('handler', 'args', 'middleware');
                }
            }
        }
        return false;
    }
}