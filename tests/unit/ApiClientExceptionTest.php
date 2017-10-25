<?php

namespace Test\Fei\ApiClient;

use Fei\ApiClient\ApiClientException;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;

/**
 * Class ApiClientExceptionTest
 *
 * @package Test\Fei\ApiClient
 */
class ApiClientExceptionTest extends TestCase
{
    public function testPreviousIsARequestException()
    {
        $requestException = $this->getMockBuilder(RequestException::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exception = new ApiClientException('message', 0, $requestException);

        $this->assertEquals($requestException, $exception->getRequestException());

        $requestException = $this->getMockBuilder(RequestException::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exception = new ApiClientException('message', 0, new \Exception('message', 0, $requestException));

        $this->assertEquals($requestException, $exception->getRequestException());
    }

    public function testPreviousIsNotARequestException()
    {
        $exception = new ApiClientException();
        $this->assertNull($exception->getRequestException());

        $exception = new ApiClientException('message', 0, new \Exception('message', 0, new \LogicException()));
        $this->assertNull($exception->getRequestException());
    }

    public function testRequestExceptionIsSet()
    {
        $requestException = $this->getMockBuilder(RequestException::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exception = new ApiClientException('message', 0, $requestException);

        $this->assertAttributeEquals(null, 'requestException', $exception);

        $this->assertEquals($requestException, $exception->getRequestException());
        $this->assertAttributeEquals($requestException, 'requestException', $exception);

        $this->assertEquals($requestException, $exception->getRequestException());
    }
}
