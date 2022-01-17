<?php

namespace Tests\Fei\ApiClient;

use Codeception\Test\Unit;
use Fei\ApiClient\AbstractApiClient;
use Fei\ApiClient\ApiClientException;
use Fei\ApiClient\ApiRequestOption;
use Fei\ApiClient\RequestDescriptor;
use Fei\ApiClient\ResponseDescriptor;
use Fei\ApiClient\Transport\AsyncTransportInterface;
use Fei\ApiClient\Transport\SyncTransportInterface;
use Fei\ApiClient\Transport\TransportException;
use Fei\Entity\AbstractEntity;
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
        $reflectedTransport = (new \ReflectionObject($client))->getProperty('transport');
        $reflectedTransport->setAccessible(true);
        $this->assertNull($client->getTransport());

        $transport = $this->createMock(SyncTransportInterface::class);

        $client->setTransport($transport);

        $this->assertSame($transport, $client->getTransport());
        $this->assertSame($transport, $reflectedTransport->getValue($client));
    }

    public function testAsyncTransportAccessors()
    {
        $client = new TestClient();
        $reflectedAsyncTransport = (new \ReflectionObject($client))->getProperty('asyncTransport');
        $reflectedAsyncTransport->setAccessible(true);
        $this->assertNull($client->getAsyncTransport());

        $transport = $this->createMock(AsyncTransportInterface::class);

        $client->setAsyncTransport($transport);

        $this->assertSame($transport, $client->getAsyncTransport());
        $this->assertSame($transport, $reflectedAsyncTransport->getValue($client));
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
        $delayNext = (new \ReflectionObject($client))->getProperty('delayNext');
        $delayNext->setAccessible(true);
        $delayedRequests = (new \ReflectionObject($client))->getProperty('delayedRequests');
        $delayedRequests->setAccessible(true);

        // ASSERT
        $this->assertSame(false, $delayNext->getValue($client));

        // RUN

        $fluent = $client->delay();

        // ASSERT
        $this->assertSame($fluent, $client);
        $this->assertSame(true, $delayNext->getValue($client));

        // PREPARE
        $request = $this->createMock(RequestDescriptor::class);
        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->never())->method('send')->with($request);
        $client->setTransport($transport);

        // RUN
        $client->send($request);

        // ASSERT
        $this->assertEquals(false, $delayNext->getValue($client));
        $this->assertEquals([[$request, ApiRequestOption::NO_RESPONSE]], $delayedRequests->getValue($client));
    }

    public function testSendMethodProxiesCallToTransportSendMethod()
    {
        // PREPARE & ASSERT
        $client = new TestClient();
        $request = $this->createMock(RequestDescriptor::class);
        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->once())->method('send')->with($request);
        $client->setTransport($transport);

        // RUN
        $client->send($request);
    }

    public function testCallingSendWithoutHavingSetASyncTransportThrowsAnException()
    {
        // PREPARE & ASSERT
        $client = new TestClient();
        $request = $this->createMock(RequestDescriptor::class);
        $this->expectException(ApiClientException::class);

        // RUN
        $client->send($request);
    }

    public function testSendMethodProxiesCallToAsyncTransportSendMethodWhenUsingNoResponseFlag()
    {
        // PREPARE & ASSERT
        $client = new TestClient();
        $request = $this->createMock(RequestDescriptor::class);
        $asyncTransport = $this->createMock(AsyncTransportInterface::class);
        $asyncTransport->expects($this->once())->method('send')->with($request);
        $client->setAsyncTransport($asyncTransport);

        // RUN
        $client->send($request, ApiRequestOption::NO_RESPONSE);
    }

    public function testAsyncRequestsAreFallingBackToSyncTransportWhenNoAsyncTransportIsSet()
    {
        // PREPARE & ASSERT
        $client = new TestClient();
        $request = $this->createMock(RequestDescriptor::class);
        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->once())->method('send')->with($request);
        $client->setTransport($transport);

        // RUN
        $client->send($request, ApiRequestOption::NO_RESPONSE);
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
        $isDelayed = (new \ReflectionObject($client))->getProperty('isDelayed');
        $isDelayed->setAccessible(true);
        $delayedRequests = (new \ReflectionObject($client))->getProperty('delayedRequests');
        $delayedRequests->setAccessible(true);
        $this->assertSame(false, $isDelayed->getValue($client));

        $fluent = $client->begin();

        $this->assertSame($fluent, $client);
        $this->assertSame(true, $isDelayed->getValue($client));

        $request = $this->createMock(RequestDescriptor::class);

        $client->send($request);
        $this->assertEquals([[$request, ApiRequestOption::NO_RESPONSE]], $delayedRequests->getValue($client));
        return $client;
    }

    public function testFetchIsSendingARequest()
    {
        $client = new TestClient();

        $request = $this->createMock(RequestDescriptor::class);
        $response = $this->createMock(ResponseDescriptor::class);
        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->once())->method('send')->with($request)->willReturn($response);
        $client->setTransport($transport);

        $entity = $client->fetch($request);
    }

    public function testFetchReturnAnEntity()
    {
        $client = new TestClient();

        $request = $this->createMock(RequestDescriptor::class);
        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->once())->method('send')->willReturnCallback(
            function () {
                return (new ResponseDescriptor())->setBody(json_encode([
                    "data" => [
                        "id" => 19
                    ],
                    "meta" => [
                        "entity" => "Tests\\Fei\\ApiClient\\TestEntity"
                    ]
                ]));
            }
        );

        $client->setTransport($transport);

        $object = $client->fetch($request);

        $this->assertEquals(new TestEntity(['id' => 19]), $object);
    }

    /**
     * @depends testBeginStartsATransaction
     */
    public function testRollbackResetDelayedRequestsAndStatus()
    {
        $client = new TestClient();
        $isDelayed = (new \ReflectionObject($client))->getProperty('isDelayed');
        $isDelayed->setAccessible(true);
        $delayedRequests = (new \ReflectionObject($client))->getProperty('delayedRequests');
        $delayedRequests->setAccessible(true);
        $client->begin();
        $request = $this->createMock(RequestDescriptor::class);
        $client->send($request);

        $fluent = $client->rollback();

        $this->assertSame(false, $isDelayed->getValue($client));
        $this->assertEquals([], $delayedRequests->getValue($client));

        $this->assertSame($fluent, $client);
    }

    /**
     * @depends testBeginStartsATransaction
     */
    public function testCommitResetDelayedRequestsAndStatus()
    {
        $client = new TestClient();
        $isDelayed = (new \ReflectionObject($client))->getProperty('isDelayed');
        $isDelayed->setAccessible(true);
        $delayedRequests = (new \ReflectionObject($client))->getProperty('delayedRequests');
        $delayedRequests->setAccessible(true);
        $client->begin();
        $request = $this->createMock(RequestDescriptor::class);
        $request2 = $this->createMock(RequestDescriptor::class);
        $client->send($request, 4);
        $client->send($request2);

        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->once())->method('sendMany')
            ->with([[$request, 4 | ApiRequestOption::NO_RESPONSE], [$request2, ApiRequestOption::NO_RESPONSE]]);
        $client->setTransport($transport);

        $fluent = $client->commit();

        $this->assertSame(false, $isDelayed->getValue($client));
        $this->assertEquals([], $delayedRequests->getValue($client));
        $this->assertSame($fluent, $client);
    }


    public function testAutoCommit()
    {
        /** @var AbstractApiClient $client */
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, [], '', true, true, true, ['commit']);
        $isDelayed = (new \ReflectionObject($client))->getProperty('isDelayed');
        $isDelayed->setAccessible(true);
        $client->enableAutoCommit();
        $this->assertEquals(true, $isDelayed->getValue($client));
    }

    public function testForceSendingARequest()
    {
        /** @var AbstractApiClient $client */
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, [], '', true, true, true, ['commit']);
        $forceNext = (new \ReflectionObject($client))->getProperty('forceNext');
        $forceNext->setAccessible(true);
        $delayNext = (new \ReflectionObject($client))->getProperty('delayNext');
        $delayNext->setAccessible(true);
        $this->assertSame(false, $forceNext->getValue($client));
        $this->assertSame(false, $delayNext->getValue($client));


        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->once())->method('send');

        $client->setTransport($transport);

        // should not actually call send
        $client->begin()->send(new RequestDescriptor());

        // should call send
        $client->begin()->force();
        $this->assertEquals(true, $forceNext->getValue($client));

        $client->send(new RequestDescriptor());
        $this->assertEquals(false, $forceNext->getValue($client));
        // check there is no interference between delay and force
        $client->delay();
        $this->assertEquals(true, $delayNext->getValue($client));

        // activating "force" should reset "delay"
        $client->force();
        $this->assertEquals(false, $delayNext->getValue($client));
        $this->assertEquals(true, $forceNext->getValue($client));

        // and vice-versa
        $client->delay();
        $this->assertEquals(true, $delayNext->getValue($client));
        $this->assertEquals(false, $forceNext->getValue($client));
    }

    public function testOptionsHandling()
    {
        $optionsValue = [
            AbstractApiClient::OPTION_BASEURL              => 'http://base-url.com',
            AbstractApiClient::OPTION_HEADER_AUTHORIZATION => 'authorizationHeaderValue'
        ];
        /** @var AbstractApiClient $client */
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, [$optionsValue], '', true, true, true, ['commit']);
        $baseUrl = (new \ReflectionObject($client))->getProperty('baseUrl');
        $baseUrl->setAccessible(true);
        $authorization = (new \ReflectionObject($client))->getProperty('authorization');
        $authorization->setAccessible(true);

        $this->assertEquals('http://base-url.com/', $baseUrl->getValue($client));
        $this->assertEquals('http://base-url.com/', $client->getBaseUrl());
        $this->assertEquals('http://base-url.com/', $client->getOption(AbstractApiClient::OPTION_BASEURL));

        $this->assertEquals('authorizationHeaderValue', $authorization->getValue($client));
        $this->assertEquals('authorizationHeaderValue', $client->getAuthorization());
        $this->assertEquals('authorizationHeaderValue', $client->getOption(AbstractApiClient::OPTION_HEADER_AUTHORIZATION));
    }

    public function testOptionsInitialization()
    {
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, [[AbstractApiClient::OPTION_BASEURL => 'http://base-url.com']], '', true, true, true, ['commit']);
        $availableOptions = (new \ReflectionObject($client))->getProperty('availableOptions');
        $availableOptions->setAccessible(true);
        $this->assertEquals(['baseUrl', 'Authorization'], $availableOptions->getValue($client));

        $client = new TestClient();
        $availableOptions = (new \ReflectionObject($client))->getProperty('availableOptions');
        $availableOptions->setAccessible(true);
        $this->assertEquals(['testOption', 'baseUrl', 'Authorization'], $availableOptions->getValue($client));
    }

    public function testSettingUnknownOptionThrowsAnException()
    {
        /**
         * @var $client AbstractApiClient
         */
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, [[AbstractApiClient::OPTION_BASEURL => 'http://base-url.com']], '', true, true, true, ['commit']);
        $this->expectException(ApiClientException::class);

        $client->setOption('unkownOption', 'any value');
    }

    public function testFallbackTransport()
    {
        $client = new TestClient();

        $asyncTransport = $this->createMock(AsyncTransportInterface::class);
        $asyncTransport->method('send')->willThrowException(new TransportException());

        $syncTransport = $this->createMock(SyncTransportInterface::class);
        $syncTransport->expects($this->once())->method('send');

        $client->setAsyncTransport($asyncTransport);
        $client->setTransport($syncTransport);

        $request = $this->createMock(RequestDescriptor::class);

        $client->send($request, ApiRequestOption::NO_RESPONSE);
    }

    public function testNoFallbackTransport()
    {
        $client = new TestClient();

        $asyncTransport = $this->createMock(AsyncTransportInterface::class);
        $asyncTransport->method('send')->willThrowException(new TransportException());

        $syncTransport = $this->createMock(SyncTransportInterface::class);
        $syncTransport->expects($this->never())->method('send');

        $client->setAsyncTransport($asyncTransport);

        $request = $this->createMock(RequestDescriptor::class);

        $this->expectException(TransportException::class);

        $client->send($request, ApiRequestOption::NO_RESPONSE);
    }

    public function testFallbackTransportIsNotUsedWhenPrimaryTransportIsOk()
    {
        $client = new TestClient();

        $asyncTransport = $this->createMock(AsyncTransportInterface::class);
        $asyncTransport->method('send')->willReturn(true);

        $syncTransport = $this->createMock(SyncTransportInterface::class);
        $syncTransport->expects($this->never())->method('send');

        $client->setAsyncTransport($asyncTransport);
        $client->setTransport($syncTransport);

        $request = $this->createMock(RequestDescriptor::class);

        $client->send($request, ApiRequestOption::NO_RESPONSE);
    }
}

class TestClient extends AbstractApiClient
{
    const OPTION_TEST = 'testOption';
}

class TestEntity extends AbstractEntity
{
    protected $id;

    /**
     * Get Id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
