<?php declare(strict_types=1);

namespace Frame;

use Psr\Http\Message\ServerRequestInterface;

final readonly class Inertia
{
    public function __construct(
        private string $rootTemplate,
        private string $manifestJsonPath
    )
    {
    }

    public function getPage(ServerRequestInterface $request, string $component, array $props = []): array
    {
        if ($request->hasHeader('X-Inertia-Partial-Data')) {
            $only = explode(',', $request->getHeaderLine('X-Inertia-Partial-Data'));
            $props = ($only && $request->getHeaderLine('X-Inertia-Partial-Component') === $component)
                ? array_intersect_key($props, array_flip($only))
                : $props;
        }

        array_walk_recursive($props, function (&$prop) {
            if ($prop instanceof \Closure) {
                $prop = $prop();
            }
        });

        return [
            'component' => $component,
            'props' => $props,
            'url' => $request->getRequestTarget(),
            'version' => $this->getAssetVersion()
        ];
    }

    public function getAssetVersion(): string
    {
        return md5_file($this->manifestJsonPath);
    }

    public function fetchRootTpl(array $props = []): string
    {
        extract($props);

        ob_start();
        include($this->rootTemplate);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public function getManifestJsonPath(): string
    {
        return $this->manifestJsonPath;
    }
}