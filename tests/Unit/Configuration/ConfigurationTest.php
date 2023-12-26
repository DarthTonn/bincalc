<?php

namespace App\Tests\Unit\Configuration;

use App\Configuration\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testGetExchangeRatesApiUrl()
    {
        $configData = [
            'exchangeRatesApiUrl' => 'http://example.com/exchange-rates',
            'binListApiUrl' => 'http://example.com/bin-list',
        ];

        $configuration = new Configuration($configData);

        $result = $configuration->getExchangeRatesApiUrl();

        $this->assertEquals($configData['exchangeRatesApiUrl'], $result);
    }

    public function testGetBinListApiUrl()
    {
        $configData = [
            'exchangeRatesApiUrl' => 'http://example.com/exchange-rates',
            'binListApiUrl' => 'http://example.com/bin-list',
        ];

        $configuration = new Configuration($configData);

        $result = $configuration->getBinListApiUrl();

        $this->assertEquals($configData['binListApiUrl'], $result);
    }
}