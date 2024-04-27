<?php declare(strict_types=1);

namespace Frame\Http\Client;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Request;

class ClientFactory
{
    private array $headers = [];
    private int $timeout = 15;
    private bool $sslCertificate = false;
    private array $files = [];

    /** Set request headers */
    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /** Set request authorization header with Bearer token */
    public function withToken(string $token): self
    {
        $this->headers['Authorization'] = 'Bearer ' . $token;
        return $this;
    }

    /** Set content type request as JSON */
    public function withJson(): self
    {
        $this->headers['Content-Type'] = 'application/json';
        return $this;
    }

    /** Set timeout */
    public function withTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /** Set check SSL certificate */
    public function withSslCertificate(bool $sslCertificate): self
    {
        $this->sslCertificate = $sslCertificate;
        return $this;
    }

    /**
     * Attach file to request.
     * @throws \RuntimeException
     */
    public function withFile(string $field, string $filepath): self
    {
        if (!file_exists($filepath)) {
            throw new \RuntimeException("File $filepath not exists");
        }

        $this->files[$field] = '@' . $filepath;
        return $this;
    }

    /** GET */
    public function get(string $uri): ResponseInterface
    {
        $request = new Request('GET', $uri);
        return $this->sendRequest($request);
    }

    /** POST */
    public function post(string $uri, array $data = []): ResponseInterface
    {
        $body = $this->encode($data);
        $request = new Request('POST', $uri, [], $body);
        return $this->sendRequest($request);
    }

    /** PUT */
    public function put(string $uri, array $data = []): ResponseInterface
    {
        $body = $this->encode($data);
        $request = new Request('PUT', $uri, [], $body);
        return $this->sendRequest($request);
    }

    /** PATCH */
    public function patch(string $uri, array $data = []): ResponseInterface
    {
        $body = $this->encode($data);
        $request = new Request('PATCH', $uri, [], $body);
        return $this->sendRequest($request);
    }

    /**  DELETE */
    public function delete(string $uri, array $data = []): ResponseInterface
    {
        $body = $this->encode($data);
        $request = new Request('DELETE', $uri, [], $body);
        return $this->sendRequest($request);
    }

    /** Encode array */
    private function encode(array $data): string
    {
        $data = array_merge($data, $this->files);
        return json_encode($data) !== false ? json_encode($data) : '';
    }

    /** Send request */
    private function sendRequest(RequestInterface $request): ResponseInterface
    {
        foreach ($this->headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        $client = new Client($this->timeout, $this->sslCertificate);
        return $client->sendRequest($request);
    }

}