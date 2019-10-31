<?php

namespace FondOfSpryker\Zed\CrossEngage\Dependency\Component\Guzzle;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

class CrossEngageToGuzzleBridge implements CrossEngageToGuzzleInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * CrossEngageToGuzzleBridge constructor.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @throws
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }

    /**
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get(string $uri, array $options = []): ResponseInterface
    {
        return $this->client->get($uri, $options);
    }

    /**
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put(string $uri, array $options = []): ResponseInterface
    {
        return $this->client->put($uri, $options);
    }

    /**
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post(string $uri, array $options = []): ResponseInterface
    {
        return $this->client->post($uri, $options);
    }
}
