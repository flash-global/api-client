<?php

namespace Tests\Fei\ApiClient;

use AspectMock\Test;
use Codeception\Test\Unit;
use Fei\ApiClient\AbstractApiClient;
use Fei\ApiClient\Request;
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
        $client = new TestClient;

        $this->assertNull($client->getTransport());

        $transport = $this->createMock(TransportInterface::class);

        $client->setTransport($transport);

        $this->assertSame($transport, $client->getTransport());
        $this->assertAttributeSame($transport, 'transport', $client);

    }

    public function testBaseUrlAccessors()
    {
        $client = new TestClient();

        $client->setBaseUrl('http://test.com');
        $this->assertEquals('http://test.com/', $client->getBaseUrl());


        $client->setBaseUrl('http://test.com/');
        $this->assertEquals('http://test.com/', $client->getBaseUrl());

    }

    public function testCallingStackMethodMakesSubsequentSendCallsStackingRequests()
    {

        // PREPARE
        $client = new TestClient();

        // RUN
        $client->stack();

        // ASSERT
        $this->assertAttributeEquals(true, 'stackNext', $client);


        // PREPARE
        $request   = $this->createMock(Request::class);
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->never())->method('send')->with($request);
        $client->setTransport($transport);

        // RUN
        $client->send($request);

        // ASSERT
        $this->assertAttributeEquals(false, 'stackNext', $client);
        $this->assertAttributeEquals([$request], 'stackedRequests', $client);

    }
    
    public function testSendMethodProxiesCallToTransportSendMethod()
    {
        // PREPARE & ASSERT
        $client = new TestClient();
        $request = $this->createMock(Request::class);
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())->method('send')->with($request);
        $client->setTransport($transport);
        
        // RUN
        $client->send($request);

    }

}

class TestClient extends AbstractApiClient
{
    
    
    
}
