<?php

namespace App\Provider;

use App\Configuration\Configuration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyProvider
{
    private Client $httpClient;
    private Configuration $config;

    /**
     * @param Configuration $config
     * @param Client $httpClient
     */
    public function __construct(Configuration $config, Client $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $currency
     * @return float
     * @throws GuzzleException
     */
    public function getRate(string $currency): float
    {
        try {
            $response = $this->httpClient->get($this->config->getExchangeRatesApiUrl());
            $data = json_decode($response->getBody(), true);

            return $data['rates'][$currency] ?? 0.0;
        } catch (\Exception $e) {
            // Handle exceptions or log errors
            return 0.0;
        }
    }
}