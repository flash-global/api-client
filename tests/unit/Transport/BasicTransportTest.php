<?php

namespace Test\Fei\ApiClient\Transport;

use Fei\ApiClient\RequestDescriptor;
use Fei\ApiClient\Response;
use Fei\ApiClient\ResponseDescriptor;
use Fei\ApiClient\Transport\BasicTransport;
use Guzzle\Http\Client;

class BasicTransportTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testBasicTransport()
    {
        
        $transport = new BasicTransport();
        $response = new \Guzzle\Http\Message\Response(200, null, '{"code": 10, "data": {"key": "value"}}');
        $client = $this->createMock(Client::class);
        $client->expects($this->once())->method('createRequest')->with('GET', 'http://test.com/api/test', array('x' => 'y'));
        $client->expects($this->once())->method('send')->willReturn($response);

        $transport->setClient($client);

        $requestDescriptor = new RequestDescriptor();
        
        $requestDescriptor->setMethod('GET');
        $requestDescriptor->setUrl('http://test.com/api/test');
        $requestDescriptor->setHeaders(array('x' => 'y'));
        
        $response = $transport->send($requestDescriptor);

        $this->assertInstanceOf(ResponseDescriptor::class, $response);

    }
}
