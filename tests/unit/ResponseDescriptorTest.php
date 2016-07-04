<?php

namespace Test\Fei\ApiClient;

use Codeception\Test\Unit;
use UnitTester;
use Fei\ApiClient\ResponseDescriptor;

/**
 * Created by PhpStorm.
 * User: arnaudpagnier
 * Date: 29/06/2016
 * Time: 22:39
 */
class ResponseDescriptorTest extends Unit
{
    /**
     * @var UnitTester;
     */
    protected $tester;

    public function testCodeAccessors()
    {
        $response = new TestResponse();

        $this->assertNull($response->getCode());

        $response->setCode(200);

        $this->assertTrue(is_int($response->getCode()));
        $this->assertGreaterThanOrEqual(100,$response->getCode());
        $this->assertLessThanOrEqual(599,$response->getCode());
        $this->assertEquals(200,$response->getCode());
        $this->assertAttributeEquals(200,'code',$response);
    }

    public function testHeadersAccessors()
    {
        $response = new TestResponse();

        $this->assertEmpty($response->getHeaders());

        $responseHeaders = array('Pragma' => 'no-cache', 'Retry-After' => 120);
        $response->setHeaders($responseHeaders);

        $this->assertSame($responseHeaders, $response->getHeaders());
        $this->assertAttributeSame($responseHeaders,'headers',$response);

    }

    public function testBodyAccessors()
    {
        $response = new TestResponse();

        $this->assertEmpty($response->getBody());

        $response->setBody('The body');

        $this->assertEquals('The body',$response->getBody());
        $this->assertAttributeEquals('The body','body',$response);
    }

    public function testDataAccessors()
    {
        $response = new TestResponse();

        $this->assertEmpty($response->getData());

        $response->setBody('{"data":[{"id":6,"message":"ere"},{"id":5,"message":"test"}],"meta":{"entity":"aze"}}');

        $this->assertEquals([['id' => 6, 'message' => 'ere'], ['id' => 5, 'message' => 'test']], $response->getData());
    }

    public function testMetaAccessors()
    {
        $response = new TestResponse();

        $this->assertEmpty($response->getMeta());

        $response->setBody(json_encode(['meta' => ['entity' => ResponseDescriptor::class , 'pagination' => ['total' => 42]]]));

        $this->assertEquals(ResponseDescriptor::class, $response->getMeta('entity'));
        $this->assertEquals(['entity' => ResponseDescriptor::class , 'pagination' => ['total' => 42]], $response->getMeta());

    }
}

class TestResponse extends ResponseDescriptor
{
}