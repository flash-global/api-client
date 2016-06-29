<?php

namespace Test\Fei\ApiClient;

use Codeception\Test\Unit;
use UnitTester;
use Fei\ApiClient\RequestDescriptor;

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
        $request = new TestRequest();

        $this->assertNull($request->getUrl());

        $request->setUrl('http://www.opcoding.eu');

        $this->assertEquals('http://www.opcoding.eu',$request->getUrl());
        $this->assertAttributeEquals('http://www.opcoding.eu','url',$request);
    }

    public function testMethodAccessors()
    {
        $request = new TestRequest();

        $this->assertNull($request->getMethod());

        $request->setMethod('POST');

        $this->assertEquals('POST',$request->getMethod());
        $this->assertAttributeEquals('POST','method',$request);
    }

    public function testParamsAccessors()
    {
        $request = new TestRequest();

        $this->assertEmpty($request->getParams());

        $requestParams = array('id' => 1, 'foo' => 'bar');
        $request->setParams($requestParams);

        $this->assertSame($requestParams, $request->getParams());
        $this->assertAttributeSame($requestParams,'params',$request);
    }

    public function testHeadersAccessors()
    {
        $request = new TestRequest();

        $this->assertEmpty($request->getHeaders());

        $requestHeaders = array('Accept' => 'text/plain', 'Accept-Charset' => 'UTF-8');
        $request->setHeaders($requestHeaders);

        $this->assertSame($requestHeaders, $request->getHeaders());
        $this->assertAttributeSame($requestHeaders,'headers',$request);

    }

    public function testBodyAccessors()
    {
        $request = new TestRequest();

        $this->assertEmpty($request->getBodyParams());

        $requestBody = array('message' => 'message value');
        $request->setBodyParams($requestBody);

        $this->assertSame($requestBody, $request->getBodyParams());
        $this->assertAttributeSame($requestBody,'bodyParams',$request);
    }

    public function testAddAParamToTheRequest()
    {
        $request = new TestRequest();

        $request->addParam('id',1);
        $request->addParam('foo','bar');

        $this->assertCount(2,$request->getParams());
        $this->assertEquals(1,$request->getParams()['id']);
        $this->assertEquals('bar',$request->getParams()['foo']);
    }

    public function testGetAParamFromAKey()
    {
        $request = new TestRequest();

        $request->addParam('id',1);

        $this->assertEquals(1,$request->getParam('id'));
    }

    public function testAddABodyParamToTheRequest()
    {
        $request = new TestRequest();

        $request->addBodyParam('message','a message');
        $request->addBodyParam('foo','bar');

        $this->assertCount(2,$request->getBodyParams());
        $this->assertEquals('a message',$request->getBodyParams()['message']);
        $this->assertEquals('bar',$request->getBodyParams()['foo']);
    }

    public function testGetABodyParamFromAKey()
    {
        $request = new TestRequest();

        $request->addBodyParam('message','a message');

        $this->assertEquals('a message',$request->getBodyParam('message'));
    }

    public function testAddAHeaderToTheRequest()
    {
        $request = new TestRequest();

        $request->addHeader('Accept','text/plain');
        $request->addHeader('Accept-Charset','UTF-8');

        $this->assertCount(2,$request->getHeaders());
        $this->assertEquals('text/plain',$request->getHeaders()['Accept']);
        $this->assertEquals('UTF-8',$request->getHeaders()['Accept-Charset']);
    }

    public function testGetAHeaderFromAKey()
    {
        $request = new TestRequest();

        $request->addHeader('Accept-Charset','UTF-8');

        $this->assertEquals('UTF-8',$request->getHeader('Accept-Charset'));
    }


}

class TestRequest extends RequestDescriptor {


}