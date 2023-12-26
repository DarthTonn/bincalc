<?php

namespace App\Configuration;

class Configuration
{
    private array $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getExchangeRatesApiUrl(): string
    {
        return $this->config['exchangeRatesApiUrl'];
    }

    /**
     * @return string
     */
    public function getBinListApiUrl(): string
    {
        return $this->config['binListApiUrl'];
    }
}