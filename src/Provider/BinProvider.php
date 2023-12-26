<?php

namespace App\Provider;

use App\Configuration\Configuration;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class BinProvider
{
    private Client $httpClient;
    private Configuration $config;

    /**
     * @param Configuration $config
     * @param Client $httpClient
     */
    public function __construct(Configuration $config, ClientInterface $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $bin
     * @return \stdClass|null
     * @throws GuzzleException
     */
    public function getBinData(string $bin): ?\stdClass
    {
        try {
            $response = $this->httpClient->get($this->config->getBinListApiUrl() . $bin);
            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return null;
        }
    }
}