<?php declare(strict_types=1);

namespace Frame\Inertia;

use Frame\Inertia;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class InertiaMiddleware implements MiddlewareInterface
{
    private Inertia $inertia;

    public function __construct(private ContainerInterface $container)
    {
        $this->inertia = $this->container->get(Inertia::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasHeader('X-Inertia')) {
            return $handler->handle($request);
        }

        $response = $handler->handle($request);

        if ($request->getMethod() === 'GET' && $request->getHeaderLine('X-Inertia-Version') !== $this->inertia->getAssetVersion()) {
            return $response->withAddedHeader('X-Inertia-Location', $request->getUri()->getPath());
        }

        if ($response->getStatusCode() === 302 && in_array($request->getMethod(), ['PUT', 'PATCH', 'DELETE'])) {
            $response = $response->withStatus(303);
        }

        return $response->withHeader('Vary', 'Accept')->withHeader('X-Inertia', 'true');
    }
}