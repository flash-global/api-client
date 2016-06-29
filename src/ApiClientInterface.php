<?php

namespace Fei\ApiClient;

use Fei\ApiClient\Transport\TransportInterface;


/**
 * Interface ApiClientInterface
 * @package Fei\ApiClient
 */
interface ApiClientInterface
{
    /**
     * @return TransportInterface
     */
    public function getTransport();

    /**
     * @param TransportInterface $transport
     *
     * @return $this
     */
    public function setTransport(TransportInterface $transport);


    public function send(RequestDescriptor $request, $flags = 0);

    public function delay();

    public function buildUrl($path);

    public function begin();

    public function rollback();


}
