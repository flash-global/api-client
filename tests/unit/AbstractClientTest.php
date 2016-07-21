<?php

namespace Tests\Fei\ApiClient;

use Codeception\Test\Unit;
use Fei\ApiClient\AbstractApiClient;
use Fei\ApiClient\ApiRequestOption;
use Fei\ApiClient\RequestDescriptor;
use Fei\ApiClient\ResponseDescriptor;
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

        // ASSERT
        $this->assertAttributeSame(false, 'delayNext', $client);

        // RUN

        $fluent = $client->delay();

        // ASSERT
        $this->assertSame($fluent, $client);
        $this->assertAttributeSame(true, 'delayNext', $client);

        // PREPARE
        $request   = $this->createMock(RequestDescriptor::class);
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->never())->method('send')->with($request);
        $client->setTransport($transport);

        // RUN
        $client->send($request);

        // ASSERT
        $this->assertAttributeEquals(false, 'delayNext', $client);
        $this->assertAttributeEquals([[$request, ApiRequestOption::NO_RESPONSE]], 'delayedRequests', $client);

    }
    
    public function testSendMethodProxiesCallToTransportSendMethod()
    {
        // PREPARE & ASSERT
        $client = new TestClient();
        $request = $this->createMock(RequestDescriptor::class);
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())->method('send')->with($request);
        $client->setTransport($transport);
        
        // RUN
        $client->send($request);

    }

    public function testUrlForging()
    {
        $client = new TestClient();
        $client->setBaseUrl('http://test.com/');

        $this->assertEquals('http://test.com/api/endpoint', $client->buildUrl('/api/endpoint'));
    }

    public function testBeginStartsATransaction()
    {
        $client = new TestClient();

        $this->assertAttributeSame(false, 'isDelayed', $client);

        $fluent = $client->begin();

        $this->assertSame($fluent, $client);
        $this->assertAttributeSame(true, 'isDelayed', $client);

        $request = $this->createMock(RequestDescriptor::class);

        $client->send($request);
        $this->assertAttributeEquals([[$request, ApiRequestOption::NO_RESPONSE]], 'delayedRequests', $client);

        return $client;
    }

    public function testFetchIsSendingARequest()
    {
        $client = new TestClient();

        $request = $this->createMock(RequestDescriptor::class);
        $response = $this->createMock(ResponseDescriptor::class);
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())->method('send')->with($request)->willReturn($response);
        $client->setTransport($transport);

        $entity = $client->fetch($request);
    }

    /**
     * @depends testBeginStartsATransaction
     */
    public function testRollbackResetDelayedRequestsAndStatus()
    {
        $client = new TestClient();
        $client->begin();
        $request = $this->createMock(RequestDescriptor::class);
        $client->send($request);

        $fluent = $client->rollback();

        $this->assertAttributeSame(false, 'isDelayed', $client);
        $this->assertAttributeEquals(array(), 'delayedRequests', $client);
        $this->assertSame($fluent, $client);
    }

    /**
     * @depends testBeginStartsATransaction
     */
    public function testCommitResetDelayedRequestsAndStatus()
    {
        $client = new TestClient();
        $client->begin();
        $request = $this->createMock(RequestDescriptor::class);
        $request2 = $this->createMock(RequestDescriptor::class);
        $client->send($request, 4);
        $client->send($request2);

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())->method('sendMany')->with([[$request, 4 |ApiRequestOption::NO_RESPONSE], [$request2, ApiRequestOption::NO_RESPONSE]]);
        $client->setTransport($transport);

        $fluent = $client->commit();

        $this->assertAttributeSame(false, 'isDelayed', $client);
        $this->assertAttributeEquals(array(), 'delayedRequests', $client);
        $this->assertSame($fluent, $client);
    }


    public function testAutoCommit()
    {
        /** @var AbstractApiClient $client */
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, array(), '', true, true, true, ['commit']);
        
        $client->enableAutoCommit();
        $this->assertAttributeEquals(true, 'isDelayed', $client);
    }

    public function testForceSendingARequest()
    {
        /** @var AbstractApiClient $client */
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, array(), '', true, true, true, ['commit']);
    
        $this->assertAttributeEquals(false, 'forceNext', $client);
        $this->assertAttributeEquals(false, 'delayNext', $client);
        
        
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())->method('send');
     
        $client->setTransport($transport);
        
        // should not actually call send
        $client->begin()->send(new RequestDescriptor());
        
        // should call send
        $client->begin()->force();
        $this->assertAttributeEquals(true, 'forceNext', $client);
        
        $client->send(new RequestDescriptor());
        $this->assertAttributeEquals(false, 'forceNext', $client);
        
        // check there is no interference between delay and force
        $client->delay();
        $this->assertAttributeEquals(true, 'delayNext', $client);
        
        // activating "force" should reset "delay"
        $client->force();
        $this->assertAttributeEquals(false, 'delayNext', $client);
        $this->assertAttributeEquals(true, 'forceNext', $client);
        
        // and vice-versa
        $client->delay();
        $this->assertAttributeEquals(true, 'delayNext', $client);
        $this->assertAttributeEquals(false, 'forceNext', $client);
    }
}

class TestClient extends AbstractApiClient
{
}
