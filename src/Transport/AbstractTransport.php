<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/06/2016
 * Time: 10:31
 */

namespace Fei\ApiClient\Transport;


use Guzzle\Http\Client;

/**
 * Class AbstractTransport
 * @package Fei\ApiClient\Transport
 */
abstract class AbstractTransport implements TransportInterface
{

    /**
     * @var Client|\Amp\Artax\Client
     */
    protected $client;

    /**
     * @return \Amp\Artax\Client|Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \Amp\Artax\Client|Client $client
     *
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

}
