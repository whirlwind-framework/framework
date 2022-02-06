<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\Rest;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Whirlwind\Infrastructure\Hydrator\Hydrator;
use Whirlwind\Infrastructure\Repository\Rest\Exception\ClientException;
use Whirlwind\Infrastructure\Repository\Rest\Exception\ServerException;

class Repository
{
    protected Hydrator $hydrator;

    protected UriFactoryInterface $uriFactory;

    protected RequestFactoryInterface $requestFactory;

    protected ClientInterface $client;

    protected string $modelClass;

    protected string $endpoint;

    protected array $headers;

    public function __construct(
        Hydrator $hydrator,
        UriFactoryInterface $uriFactory,
        RequestFactoryInterface $requestFactory,
        ClientInterface $client,
        string $modelClass,
        string $endpoint
    ) {
        $this->hydrator = $hydrator;
        $this->uriFactory = $uriFactory;
        $this->requestFactory = $requestFactory;
        $this->client = $client;
        $this->modelClass = $modelClass;
        $this->endpoint = $endpoint;
    }

    public function findById($id): object
    {
        $url = \rtrim($this->endpoint, '/') . '/' . $id;
        $response = $this->request('GET', $this->uriFactory->createUri($url));
        return $this->hydrator->hydrate($this->modelClass, $response['body']);
    }

    public function findAll(
        array $conditions = []
    ): array {
        $uri = $this->uriFactory->createUri(\rtrim($this->endpoint, '/'))
            ->withQuery(\http_build_query($conditions));
        $response = $this->request('GET', $uri);
        $result = [];
        foreach ($response['body'] as $item) {
            $result[] = $this->hydrator->hydrate($this->modelClass, $item);
        }
        return $result;
    }

    public function addHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    public function addToken(string $token)
    {
        if (!empty($token)) {
            $this->addHeader('Authorization', $token);
        }
    }

    /**
     * @throws \Throwable
     */
    protected function request(string $method, UriInterface $uri, StreamInterface $body = null): array
    {
        $this->addHeader('Accept', 'application/json');

        $request = $this->requestFactory->createRequest($method, $uri);

        if ($body !== null) {
            $request = $request->withBody($body);
        }

        foreach ($this->headers as $headerName => $headerValue) {
            $request = $request->withAddedHeader($headerName, $headerValue);
        }

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            $message = \sprintf('Failed to to perform request to service (%s).', $e->getMessage());
            throw new ServerException($message, $e->getCode());
        }

        if ($response->getStatusCode() > 399) {
            throw $this->createException($response);
        }

        $data = \json_decode($response->getBody()->getContents(), true);

        return [
            'headers' => $response->getHeaders(),
            'body' => $data
        ];
    }

    protected function createException(ResponseInterface $responseWithException): \Throwable
    {
        $responseCodeLevel = (int) \floor($responseWithException->getStatusCode() / 100);
        $message = $this->formatResponseExceptionMessage($responseWithException);

        if ($responseCodeLevel === 4) {
            return new ClientException($responseWithException->getStatusCode(), $message);
        }

        return new ServerException($message, $responseWithException->getStatusCode());
    }

    protected function formatResponseExceptionMessage(ResponseInterface $responseWithException): string
    {
        return \sprintf(
            'Service responded with error (%s - %s). %s',
            $responseWithException->getStatusCode(),
            $responseWithException->getReasonPhrase(),
            $responseWithException->getBody()->getContents()
        );
    }

    public function generateHeaders(array $response): array
    {
        $headers = [];
        foreach ($this->headers as $header => $value) {
            if (isset($response[$header][0])) {
                $headers[$value] = $response[$header];
            }
        }
        return $headers;
    }
}
