<?php

namespace Test\Fei\ApiClient\Transport;

use Fei\ApiClient\RequestDescriptor;
use Fei\ApiClient\ResponseDescriptor;
use Fei\ApiClient\Transport\BasicTransport;
use Fei\ApiClient\Transport\Psr7\RequestFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class BasicTransportTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testBasicTransport()
    {
        
        $transport = new BasicTransport();
        $requestDescriptor = new RequestDescriptor();



        $factoryMock = $this->getMockBuilder(RequestFactory::class)->setMethods(['create'])->getMock();

        $response = new Response(200, [], '{"code": 10, "data": {"key": "value"}}');
        $client = $this->createMock(Client::class);
        $client->expects($this->once())->method('send')->willReturn($response);

        $request = new Request(
            'GET',
            'http://test.com/api/test',
            array('x' => 'y')
        );
        $factoryMock->expects($this->once())->method('create')->with($requestDescriptor)->willReturn($request);

        $transport->setRequestFactory($factoryMock);
        $transport->setClient($client);
        
        $requestDescriptor->setMethod('GET');
        $requestDescriptor->setUrl('http://test.com/api/test');
        $requestDescriptor->setHeaders(array('x' => 'y'));
        
        $response = $transport->send($requestDescriptor);

        $this->assertInstanceOf(ResponseDescriptor::class, $response);
        $this->assertEquals($client, $transport->getClient());

    }

    public function testAccessorsRequestFactory()
    {
        $this->testOneAccessors('requestFactory', new RequestFactory());
    }

    protected function testOneAccessors($name, $expected)
    {
        $setter = 'set' . ucfirst($name);
        $getter = 'get' . ucfirst($name);

        $basicTransport = new BasicTransport();
        $basicTransport->$setter($expected);

        $this->assertEquals($basicTransport->$getter(), $expected);
        $this->assertAttributeEquals($basicTransport->$getter(), $name, $basicTransport);
    }
}
