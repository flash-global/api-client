<?php

namespace Test\Fei\ApiClient\Transport\Psr7;

use Fei\ApiClient\RequestDescriptor;
use Fei\ApiClient\Transport\Psr7\RequestFactory;
use Psr\Http\Message\RequestInterface;

class RequestFactoryTest extends \Codeception\Test\Unit
{

    /**
     * @dataProvider methodProvider
     */
    public function testCreate($method)
    {

        $requestDescriptorMock = $this->getMockBuilder(RequestDescriptor::class)->setMethods(['getMethod', 'getBodyParams'])->getMock();
        $requestDescriptorMock->expects($this->any())->method('getMethod')->willReturn($method);
        $requestDescriptorMock->expects($this->any())->method('getBodyParams')->willReturn(['toto' => 'titi']);

        $requestFactory = new RequestFactory();
        $this->assertInstanceOf(RequestInterface::class, $requestFactory->create($requestDescriptorMock));
    }

    public function methodProvider()
    {
        return [
            ['GET'],
            ['POST'],
            ['DELETE'],
            ['PUT'],
            ['OPTIONS'],
            ['PATCH']
        ];
    }
}
