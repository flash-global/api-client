<?php

namespace Test\Pricer\WebClient\Transport;

class ClientTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    public function testAbstractTransport()
    {
        $client = new TestClient();
        $transport = $client->getTransport();

        $this->assertEquals($client->getTransport(), $transport);

        $client->setTransport($transport);

        $this->assertEquals($transport, $client->getTransport());

    }

}

class TestClient extends \Pricer\WebClient\AbstractClient
{
}