<?php

namespace App\Tests\Unit\Provider;

use App\Provider\CurrencyProvider;
use App\Configuration\Configuration;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CurrencyProviderTest extends TestCase
{

    private MockObject $config;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->config = $this->createMock(Configuration::class);
    }

    public function testGetRateSuccess()
    {
        $this->config->method('getExchangeRatesApiUrl')->willReturn('http://example.com/api/exchange-rates');

        $httpClient = $this->createMock(Client::class);
        $httpClient->method('get')->willReturn(new Response(200, [], '{"rates":{"USD":1.2}}'));

        $currencyProvider = new CurrencyProvider($this->config, $httpClient);

        $rate = $currencyProvider->getRate('USD');

        $this->assertEquals(1.2, $rate);
    }

    public function testGetRateFailure()
    {
        $this->config->method('getExchangeRatesApiUrl')->willReturn('http://example.com/api/exchange-rates');

        $httpClient = $this->createMock(Client::class);
        $httpClient->method('get')->willThrowException(
            new RequestException(
                'Request failed',
                $this->createMock(\Psr\Http\Message\RequestInterface::class)
            )
        );

        $currencyProvider = new CurrencyProvider($this->config, $httpClient);

        $rate = $currencyProvider->getRate('USD');

        $this->assertEquals(0.0, $rate);
    }
}