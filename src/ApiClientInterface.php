<?php

namespace Fei\ApiClient;

use Fei\ApiClient\Transport\SyncTransportInterface;
use Fei\ApiClient\Transport\TransportInterface;
use Fei\Entity\EntityInterface;

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
     * @param SyncTransportInterface $transport
     *
     * @return $this
     */
    public function setTransport(SyncTransportInterface $transport);

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
     * @return EntityInterface
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
