<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Whirlwind\Infrastructure\Hydrator\Hydrator;
use Whirlwind\Infrastructure\Repository\Rest\Exception\ClientException;
use Whirlwind\Infrastructure\Repository\Rest\Exception\ServerException;

class Repository
{
    /**
     * @var Hydrator
     */
    protected $hydrator;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $modelClass;

    protected $endpoint;

    /**
     * @var array
     */
    protected $headers;

    /**
     * RestRepository constructor.
     * @param Hydrator $hydrator
     * @param Client $client
     * @param string $modelClass
     * @param string $collectionClass
     * @param string $endpoint
     */
    public function __construct(
        Hydrator $hydrator,
        Client $client,
        string $modelClass,
        string $endpoint
    ) {
        $this->hydrator = $hydrator;
        $this->client = $client;
        $this->modelClass = $modelClass;
        $this->endpoint = $endpoint;
    }

    public function findById($id): object
    {
        $url = \rtrim($this->endpoint, '/') . '/' . $id;
        $response = $this->request('get', $url);
        return $this->hydrator->hydrate($this->modelClass, $response['body']);
    }

    public function findAll(
        array $conditions = []
    ): array {
        $url = \rtrim($this->endpoint, '/');
        $response = $this->request('get', $url, [RequestOptions::QUERY => $conditions]);
        $result = [];
        foreach ($response['body'] as $item) {
            $result[] = $this->hydrator->hydrate($this->modelClass, $item);
        }
        return $result;
    }

    /**
     * @param string $header
     * @param string $value
     * @return $this
     */
    public function addHeader(string $header, string $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * @param string $token
     */
    public function addToken(string $token)
    {
        if (!empty($token)) {
            $this->addHeader('Authorization', $token);
        }
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @return array
     */
    protected function request(string $method, string $url, array $params = []): array
    {
        $this->addHeader('Accept', 'application/json');
        $params['headers'] = $this->headers;

        try {
            /** @var \Psr\Http\Message\ResponseInterface $userRequest */
            $userRequest = $this->client->{$method}($url, $params);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $message = $this->formatResponseExceptionMessage($e);
            throw new ClientException($e->getResponse()->getStatusCode(), $message);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $message = $this->formatResponseExceptionMessage($e);
            throw new ServerException($message, $e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            $message = \sprintf('Failed to to perform request to service (%s).', $e->getMessage());
            throw new ServerException($message, $e->getCode());
        }

        $data = \json_decode($userRequest->getBody()->getContents(), true);

        return [
            'headers' => $userRequest->getHeaders(),
            'body' => $data
        ];
    }

    /**
     * @param BadResponseException $e
     * @return string
     */
    private function formatResponseExceptionMessage(BadResponseException $e): string
    {
        $message = \sprintf(
            'Service responded with error (%s - %s).',
            $e->getResponse()->getStatusCode(),
            $e->getResponse()->getReasonPhrase()
        );
        $message .= "\n" . $e->getResponse()->getBody()->getContents();

        return $message;
    }

    /**
     * @param array $response
     * @return array
     */
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
