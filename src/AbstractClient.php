<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/06/2016
 * Time: 10:29
 */

namespace Pricer\WebClient;


use Pricer\WebClient\Transport\TransportInterface;

/**
 * Class AbstractClient
 * @package Pricer\WebClient
 */
abstract class AbstractClient implements ClientInterface
{

    /** @var  TransportInterface */
    protected $transport;

    /**
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param TransportInterface $transport
     *
     * @return $this
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;

        return $this;
    }
}