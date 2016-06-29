<?php

namespace Test\Fei\ApiClient\Transport;

use Fei\ApiClient\Transport\AsyncTransport;
use Fei\ApiClient\Transport\BasicTransport;

class TransportTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  string */
    protected $baseUri;


    protected function _before()
    {
        $this->baseUri = "http://httpbin.org";
    }

    public function testBasicTransport()
    {
        $transport = new BasicTransport();

        $this->tester->wantTo('get request');

        $url = $this->baseUri . '/get';
        $request = $transport->get($url);

        $this->assertInstanceOf('\Guzzle\Http\Message\Request', $request);
        $this->assertEquals($url, $request->getUrl());
        $this->assertEquals('GET', $request->getMethod());

        $this->tester->wantToTest('post request');

        $url = $this->baseUri . '/post';
        $request = $transport->post(json_encode('hello world'), $url);
        $request2 = $transport->post(json_encode('hello world'), $url);

        $this->assertInstanceOf('\Guzzle\Http\Message\Request', $request);
        $this->assertEquals($url, $request->getUrl());
        $this->assertEquals('POST', $request->getMethod());

        $this->tester->wantToTest('send request');

        $this->tester->assertThrows(function () use ($transport) {
            $class = new \stdClass();
            $transport->send($class);
        }, 'Exception');

        $response = $transport->send($request);

        $this->assertInstanceOf('Guzzle\Http\Message\Response', $response);
        $this->assertContains('hello world', $response->getBody(true));

        $this->tester->wantToTest('send many requests');

        $requests['req1'] = $request;
        $requests['req2'] = $request2;

        $responses = $transport->sendMany($requests);

        $this->assertTrue(is_array($responses));

        /** @var \Guzzle\Http\Message\Response $response */
        foreach ($responses as $response) {
            $this->assertContains('hello world', $response->getBody(true));
        }

    }


    public function testAsyncTransport(){

        $transport = new AsyncTransport();

        $this->tester->wantTo('get request');

        $url = $this->baseUri . '/get';
        $request = $transport->get($url);

        $this->assertInstanceOf('Amp\Artax\Request', $request);
        $this->assertEquals($url, $request->getUri());
        $this->assertEquals('GET', $request->getMethod());

        $this->tester->wantToTest('post request');

        $url = $this->baseUri . '/post';
        $request = $transport->post(json_encode('hello world'), $url);
        $request2 = $transport->post(json_encode('hello world'), $url);

        $this->assertInstanceOf('Amp\Artax\Request', $request);
        $this->assertEquals($url, $request->getUri());
        $this->assertEquals('POST', $request->getMethod());

        $this->tester->wantToTest('send request');

        $this->tester->assertThrows(function () use ($transport) {
            $class = new \stdClass();
            $transport->send($class);
        }, 'Exception', 'An exception as to be thrown.');

        $response = $transport->send($request);

        $this->assertInstanceOf('Amp\Promise', $response);

        /** @var \Amp\Artax\Response $syncresponse */
        $syncresponse = \Amp\wait($response);

        $this->assertInstanceOf('Amp\Artax\Response', $syncresponse);
        $this->assertContains('hello world', $syncresponse->getBody());

        $this->tester->wantToTest('send many requests');

        $requests['req1'] = $request;
        $requests['req2'] = $request2;

        // with specified url
        $responses = $transport->sendMany($requests, $url);

        $this->assertTrue(is_array($responses));

        $waitresponses = \Amp\wait(\Amp\all($responses));

        /** @var \Amp\Artax\Response $response */
        foreach ($waitresponses as $response) {
            $this->assertContains('hello world', $response->getBody());
        }

        // without specified url
        $responses = $transport->sendMany($requests);

        $this->tester->assertThrows(function () use ($transport) {
            $class = new \stdClass();
            $transport->sendMany([$class]);
        }, 'Exception', 'An exception as to be thrown.');

        $this->assertTrue(is_array($responses));

        $waitresponses = \Amp\wait(\Amp\all($responses));

        /** @var \Amp\Artax\Response $response */
        foreach ($waitresponses as $response) {
            $this->assertContains('hello world', $response->getBody());
        }

    }


    public function testAbstractTransport()
    {
        $transport = new BasicTransport();
        $client = $transport->getClient();

        $this->assertEquals($transport->getClient(), $client);

        $transport->setClient($client);

        $this->assertEquals($client, $transport->getClient());

    }

}
