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
        $client = new TestClient;

        $this->assertNull($client->getTransport());

        $transport = $this->createMock(SyncTransportInterface::class);

        $client->setTransport($transport);

        $this->assertSame($transport, $client->getTransport());
        $this->assertAttributeSame($transport, 'transport', $client);

    }

    public function testAsyncTransportAccessors()
    {
        $client = new TestClient;

        $this->assertNull($client->getAsyncTransport());

        $transport = $this->createMock(AsyncTransportInterface::class);

        $client->setAsyncTransport($transport);

        $this->assertSame($transport, $client->getAsyncTransport());
        $this->assertAttributeSame($transport, 'asyncTransport', $client);

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
        $request = $this->createMock(RequestDescriptor::class);
        $transport = $this->createMock(SyncTransportInterface::class);
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
        $client->begin();
        $request = $this->createMock(RequestDescriptor::class);
        $client->send($request);

        $fluent = $client->rollback();

        $this->assertAttributeSame(false, 'isDelayed', $client);
        $this->assertAttributeEquals([], 'delayedRequests', $client);
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

        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->once())->method('sendMany')
            ->with([[$request, 4 | ApiRequestOption::NO_RESPONSE], [$request2, ApiRequestOption::NO_RESPONSE]]);
        $client->setTransport($transport);

        $fluent = $client->commit();

        $this->assertAttributeSame(false, 'isDelayed', $client);
        $this->assertAttributeEquals([], 'delayedRequests', $client);
        $this->assertSame($fluent, $client);
    }


    public function testAutoCommit()
    {
        /** @var AbstractApiClient $client */
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, [], '', true, true, true, ['commit']);

        $client->enableAutoCommit();
        $this->assertAttributeEquals(true, 'isDelayed', $client);
    }

    public function testForceSendingARequest()
    {
        /** @var AbstractApiClient $client */
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, [], '', true, true, true, ['commit']);

        $this->assertAttributeEquals(false, 'forceNext', $client);
        $this->assertAttributeEquals(false, 'delayNext', $client);


        $transport = $this->createMock(SyncTransportInterface::class);
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

    public function testOptionsHandling()
    {
        $optionsValue = [
            AbstractApiClient::OPTION_BASEURL              => 'http://base-url.com',
            AbstractApiClient::OPTION_HEADER_AUTHORISATION => 'authorizationHeaderValue'
        ];
        /** @var AbstractApiClient $client */
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, [$optionsValue], '', true, true, true, ['commit']);

        $this->assertAttributeEquals('http://base-url.com/', 'baseUrl', $client);
        $this->assertEquals('http://base-url.com/', $client->getBaseUrl());
        $this->assertEquals('http://base-url.com/', $client->getOption(AbstractApiClient::OPTION_BASEURL));

        $this->assertAttributeEquals('authorizationHeaderValue', 'authorization', $client);
        $this->assertEquals('authorizationHeaderValue', $client->getAuthorization());
        $this->assertEquals('authorizationHeaderValue', $client->getOption(AbstractApiClient::OPTION_HEADER_AUTHORISATION));
    }

    public function testOptionsInitialization()
    {
        $client = $this->getMockForAbstractClass(AbstractApiClient::class, [[AbstractApiClient::OPTION_BASEURL => 'http://base-url.com']], '', true, true, true, ['commit']);
        $this->assertAttributeEquals(['baseUrl', 'authorization'], 'availableOptions', $client);

        $client = new TestClient();
        $this->assertAttributeEquals(['testOption', 'baseUrl', 'authorization'], 'availableOptions', $client);
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
