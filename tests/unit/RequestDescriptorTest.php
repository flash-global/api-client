<?php

namespace Test\Fei\ApiClient;

use Codeception\Test\Unit;
use Fei\ApiClient\RequestDescriptor;
use UnitTester;

/**
 * Created by PhpStorm.
 * User: arnaudpagnier
 * Date: 29/06/2016
 * Time: 20:19
 */
class RequestDescriptorTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testUrlAccessors()
    {
        $request = new RequestDescriptor();

        $this->assertNull($request->getUrl());

        $request->setUrl('http://www.opcoding.eu');

        $this->assertEquals('http://www.opcoding.eu', $request->getUrl());
        $this->assertAttributeEquals('http://www.opcoding.eu', 'url', $request);
    }

    public function testMethodAccessors()
    {
        $request = new RequestDescriptor();

        $this->assertNull($request->getMethod());

        $request->setMethod('POST');

        $this->assertEquals('POST', $request->getMethod());
        $this->assertAttributeEquals('POST', 'method', $request);
    }

    public function testParamsAccessors()
    {
        $request = new RequestDescriptor();

        $this->assertEmpty($request->getParams());

        $requestParams = array('id' => 1, 'foo' => 'bar');
        $request->setParams($requestParams);

        $this->assertSame($requestParams, $request->getParams());
        $this->assertAttributeSame($requestParams, 'params', $request);
    }

    public function testHeadersAccessors()
    {
        $request = new RequestDescriptor();

        $this->assertEmpty($request->getHeaders());

        $requestHeaders = array('Accept' => 'text/plain', 'Accept-Charset' => 'UTF-8');
        $request->setHeaders($requestHeaders);

        $this->assertSame($requestHeaders, $request->getHeaders());
        $this->assertAttributeSame($requestHeaders, 'headers', $request);

    }

    public function testBodyAccessors()
    {
        $request = new RequestDescriptor();

        $this->assertEmpty($request->getBodyParams());

        $requestBody = array('message' => 'message value');
        $request->setBodyParams($requestBody);

        $this->assertSame($requestBody, $request->getBodyParams());
        $this->assertAttributeSame($requestBody, 'bodyParams', $request);
    }

    public function testAddAParamToTheRequest()
    {
        $request = new RequestDescriptor();

        $request->addParam('id', 1);
        $request->addParam('foo', 'bar');

        $this->assertCount(2, $request->getParams());
        $this->assertEquals(1, $request->getParams()['id']);
        $this->assertEquals('bar', $request->getParams()['foo']);
    }

    public function testGetAParamFromAKey()
    {
        $request = new RequestDescriptor();

        $request->addParam('id', 1);

        $this->assertEquals(1, $request->getParam('id'));
    }

    public function testAddABodyParamToTheRequest()
    {
        $request = new RequestDescriptor();

        $request->addBodyParam('message', 'a message');
        $request->addBodyParam('foo', 'bar');

        $this->assertCount(2, $request->getBodyParams());
        $this->assertEquals('a message', $request->getBodyParams()['message']);
        $this->assertEquals('bar', $request->getBodyParams()['foo']);
    }

    public function testGetABodyParamFromAKey()
    {
        $request = new RequestDescriptor();

        $request->addBodyParam('message', 'a message');

        $this->assertEquals('a message', $request->getBodyParam('message'));
    }

    public function testAddAHeaderToTheRequest()
    {
        $request = new RequestDescriptor();

        $request->addHeader('Accept', 'text/plain');
        $request->addHeader('Accept-Charset', 'UTF-8');

        $this->assertCount(2, $request->getHeaders());
        $this->assertEquals('text/plain', $request->getHeaders()['Accept']);
        $this->assertEquals('UTF-8', $request->getHeaders()['Accept-Charset']);
    }

    public function testGetAHeaderFromAKey()
    {
        $request = new RequestDescriptor();

        $request->addHeader('Accept-Charset', 'UTF-8');

        $this->assertEquals('UTF-8', $request->getHeader('Accept-Charset'));
    }

    public function testToArrayConversionAndHydration()
    {
        $request = new RequestDescriptor();

        $request->setUrl('test');
        $request->setMethod('POST');
        $request->setBodyParams(['a' => 'x']);
        $request->setParams(['b' => 'y']);
        $request->setHeaders(['HTTP_HEADER' => 'value']);

        $this->assertArraySubset(['url' => 'test', 'method' => 'POST', 'bodyParams' => ['a' => 'x'], 'params' => ['b' => 'y'], 'headers' => ['HTTP_HEADER' => 'value']], $request->toArray());

        $restoredRequest = new RequestDescriptor($request->toArray());

        $this->assertEquals($request, $restoredRequest);
    }

    public function testFetchBodyLogicException()
    {
        $requestDescriptor = (new RequestDescriptor())
        ->setBodyParams(['toto' => 'titi'])
        ->setRawData('my raw data');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The body param and raw data can not be both filled');

        $requestDescriptor->fetchBody();
    }

    public function testFetchBodyWithBodyParamsNotEmpty()
    {
        $requestDescriptor = (new RequestDescriptor())
            ->setBodyParams(['test' => 'test2']);

        $this->assertEquals("test=test2", $requestDescriptor->fetchBody());
    }

    public function testFetchBodyWithRawDataNotEmpty()
    {
        $requestDescriptor = (new RequestDescriptor())
            ->setRawData("test");

        $this->assertEquals("test", $requestDescriptor->fetchBody());
    }

    public function testFetchBodyNull()
    {
        $requestDescriptor = (new RequestDescriptor());
        $this->assertNull($requestDescriptor->fetchBody());
    }

}
