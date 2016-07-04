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

    /**
     * @param RequestDescriptor $request
     * @param int               $flags
     *
     * @return ResponseDescriptor
     */
    public function send(RequestDescriptor $request, $flags = 0);

    /**
     * @param RequestDescriptor $request
     * @param int               $flags
     *
     * @return Fei\Entity\EntityInterface
     */
    public function fetch(RequestDescriptor $request, $flags = 0);

    /**
     * @return ApiClientInterface
     */
    public function delay();

    /**
     * @param $path
     *
     * @return string
     */
    public function buildUrl($path);

    /**
     * @return ApiClientInterface
     */
    public function begin();

    /**
     * @return ApiClientInterface
     */
    public function rollback();

}
