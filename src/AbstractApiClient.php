<?php

namespace Fei\ApiClient;

use Fei\ApiClient\Transport\TransportInterface;
use Fei\Entity\EntityInterface;

/**
 * Class AbstractApiApiClient
 *
 * @package Fei\ApiClient
 */
abstract class AbstractApiClient implements ApiClientInterface
{

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var  TransportInterface
     */
    protected $transport;

    /**
     * @var bool
     */
    protected $delayNext = false;

    /**
     * @var array
     */
    protected $delayedRequests;

    /**
     * @var bool
     */
    protected $isDelayed = false;

    /**
     * @var bool
     */
    protected $autoCommit = true;

    /**
     * Forge complete URL
     *
     * @param $path string Path part of the URL
     *
     * @return string
     */
    public function buildUrl($path)
    {
        return $this->getBaseUrl() . ltrim($path, '/');
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';

        return $this;
    }

    /**
     * Tells client to stack the next request
     */
    public function delay()
    {
        $this->delayNext = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function rollback()
    {
        $this->isDelayed = false;
        $this->delayedRequests = array();

        return $this;
    }

    /**
     * @param RequestDescriptor $request
     * @param int               $flags
     *
     * @return EntityInterface
     */
    public function fetch(RequestDescriptor $request, $flags = 0)
    {
        $response = $this->send($request, $flags);
        $class = $response->getMeta('entity');

        $data = $response->getData();
        $entity = array();

        if (!empty($class)) {

            if (is_array($data[0])) {
                foreach ($data as $resource) {

                    /** @var EntityInterface $entity */
                    $subEntity = new $class;
                    $entity->hydrate($resource);
                    $entity[] = $subEntity;
                }
            } else {

                /** @var EntityInterface $entity */
                $entity = new $class;
                $entity->hydrate($response->getData());
            }
        }

        return $entity;
    }

    /**
     * @param RequestDescriptor $request
     *
     * @param int               $flags
     *
     * @return ResponseDescriptor
     */
    public function send(RequestDescriptor $request, $flags = 0)
    {
        if ($this->delayNext || $this->isDelayed) {
            $this->delayedRequests[] = array($request, $flags | ApiRequestOption::NO_RESPONSE);

            // reset stackNext flag
            $this->delayNext = false;

            return null;
        }

        $response = $this->getTransport()->send($request, $flags);

        return $response;
    }

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
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;

        return $this;
    }

    /**
     * @return $this
     */
    public function begin()
    {
        $this->isDelayed = true;

        return $this;
    }

    public function commit()
    {
        $this->isDelayed = false;

        if(!empty($this->delayedRequests)){
            $this->sendMany($this->delayedRequests);
        }

        $this->delayedRequests = array();

        return $this;
    }

    public function enableAutoCommit(){
        $this->begin();

        $instance = $this;
        register_shutdown_function(function () use ($instance) {
            if ($instance->getTransport() && !empty($instance->delayedRequests) && $instance->autoCommit) {
                $instance->commit();
            }
        });

        $this->autoCommit = true;

        return $this;
    }

    public function disableAutoCommit(){
        $this->autoCommit = false;

        return $this;
    }

    public function sendMany(array $delayedRequests)
    {
        $response = $this->getTransport()->sendMany($delayedRequests);

        return $response;
    }
}
