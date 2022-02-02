<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\Rest;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
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
            ->withQuery(implode('&', $conditions));
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

    protected function request(string $method, UriInterface $uri): array
    {
        $this->addHeader('Accept', 'application/json');

        $request = $this->requestFactory->createRequest($method, $uri);

        foreach ($this->headers as $headerName => $headerValue) {
            $request = $request->withAddedHeader($headerName, $headerValue);
        }

        try {
            $userRequest = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            $level = (int) \floor($e->getCode() / 100);

            if ($level === 4) {
                $message = $this->formatResponseExceptionMessage($e);
                throw new ClientException($e->getCode(), $message);
            }

            if ($level === 5) {
                $message = $this->formatResponseExceptionMessage($e);
                throw new ServerException($message, $e->getCode());
            }

            $message = \sprintf('Failed to to perform request to service (%s).', $e->getMessage());
            throw new ServerException($message, $e->getCode());
        }

        $data = \json_decode($userRequest->getBody()->getContents(), true);

        return [
            'headers' => $userRequest->getHeaders(),
            'body' => $data
        ];
    }

    private function formatResponseExceptionMessage(ClientExceptionInterface $e): string
    {
        return "Service responded with error ({$e->getCode()}).\n{$e->getMessage()}";
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
