<?php declare(strict_types=1);

namespace Frame;

use Frame\Router\RouteCollector;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Application extends RouteCollector implements RequestHandlerInterface
{
    private int $index = 0;
    private array $middlewares = [];
    private Psr17Factory $psr17Factory;

    public function __construct(
        private readonly ContainerInterface $container
    )
    {
        $this->psr17Factory = $this->container->get(Psr17Factory::class);
    }

    public function addMiddleware($middleware): void
    {
        $this->middlewares[] = is_string($middleware) ? new $middleware($this->container) : $middleware;
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $routeInfo = $this->matchRoute($request->getMethod(), $request->getUri()->getPath());

        if ($routeInfo === false) {
            return $this->psr17Factory->createResponse(404);
        }

        array_map([$this, 'addMiddleware'], $routeInfo['middleware']);

        $request = $request->withAttribute('routeInfo', $routeInfo)->withAttribute('routes', $this->routes);

        return $this->handle($request);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!isset($this->middlewares[$this->index])) {
            return $this->routeDispatcher($request);
        }
        $this->index++;
        return $this->middlewares[$this->index - 1]->process($request, $this);
    }

    private function routeDispatcher(ServerRequestInterface $request): ResponseInterface
    {
        $routeInfo = $request->getAttribute('routeInfo');
        $response = $this->psr17Factory->createResponse();

        if (is_callable($routeInfo['handler'])) {
            return call_user_func($routeInfo['handler'], $request, $response, $routeInfo['args']);
        }

        $controller = $this->container->get($routeInfo['handler'][0]);
        $method = $routeInfo['handler'][1];
        return $controller->$method($request, $response, $routeInfo['args']);
    }
}