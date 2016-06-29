<?php

namespace Tests\Fei\ApiClient;

use Codeception\Test\Unit;
use Fei\ApiClient\AbstractApiClient;
use Fei\ApiClient\Transport\TransportInterface;
use UnitTester;

class ClientTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    
    public function testTransportAccessors()
    {
        $client = new TestClient();

        $this->assertNull($client->getTransport());


        $transport = $this->createMock(TransportInterface::class);

        $client->setTransport($transport);

        $this->assertSame($transport, $client->getTransport());

    }

    public function testBaseUrlAccessor()
    {
        $client = new TestClient();

        $client->setBaseUrl('http://test.com');

        $this->assertEquals('http://test.com/', $client->getBaseUrl());

    }

}

class TestClient extends AbstractApiClient
{
    
}
