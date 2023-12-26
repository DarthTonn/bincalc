<?php

namespace App\Tests\Unit\Provider;

use App\Provider\BinProvider;
use App\Configuration\Configuration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BinProviderTest extends TestCase
{

    private MockObject $config;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->config = $this->createMock(Configuration::class);
    }

    public function testGetBinDataSuccess()
    {
        $this->config->method('getBinListApiUrl')->willReturn('http://example.com/api/bin/');

        $httpClient = $this->createMock(Client::class);
        $httpClient->method('get')->willReturn(new Response(200, [], '{"country":{"alpha2":"US"}}'));

        $binProvider = new BinProvider($this->config, $httpClient);

        $binData = $binProvider->getBinData('123456');

        $this->assertInstanceOf(\stdClass::class, $binData);
        $this->assertEquals('US', $binData->country->alpha2);
    }

    public function testGetBinDataFailure()
    {
        $this->config->method('getBinListApiUrl')->willReturn('http://example.com/api/bin/');

        $httpClient = $this->createMock(Client::class);
        $httpClient->method('get')->willThrowException(
            new RequestException(
                'Request failed',
                $this->createMock(\Psr\Http\Message\RequestInterface::class)
            )
        );

        $binProvider = new BinProvider($this->config, $httpClient);

        $binData = $binProvider->getBinData('123456');

        $this->assertNull($binData);
    }
}