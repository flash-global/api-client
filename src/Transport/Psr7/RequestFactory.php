<?php

namespace Fei\ApiClient\Transport\Psr7;

use Fei\ApiClient\RequestDescriptor;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * Class RequestFactory
 *
 * @package Fei\ApiClient\Transport\Psr7
 */
class RequestFactory
{
    /**
     * @param RequestDescriptor $descriptor
     *
     * @return RequestInterface
     */
    public function create(RequestDescriptor $descriptor)
    {
        $method = strtolower($descriptor->getMethod());

        if (!method_exists($this, $method)) {
            return $this->defaultRequest($descriptor);
        }

        return $this->$method($descriptor);
    }

    /**
     * @param RequestDescriptor $descriptor
     *
     * @return RequestInterface
     */
    protected function get(RequestDescriptor $descriptor)
    {
        return new Request(
            $descriptor->getMethod(),
            $descriptor->getUrl(),
            $descriptor->getHeaders()
        );
    }

    /**
     * @param RequestDescriptor $descriptor
     *
     * @return RequestInterface
     */
    protected function post(RequestDescriptor $descriptor)
    {
        if (!array_key_exists('Content-Type', $descriptor->getHeaders()) && !empty($descriptor->getBodyParams())) {
            $descriptor->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
        
        $request = new Request(
            $descriptor->getMethod(),
            $descriptor->getUrl(),
            $descriptor->getHeaders(),
            $descriptor->fetchBody()
        );

        return $request;
    }

    /**
     * @param RequestDescriptor $descriptor
     *
     * @return RequestInterface
     */
    protected function defaultRequest(RequestDescriptor $descriptor)
    {
        return new Request(
            $descriptor->getMethod(),
            $descriptor->getUrl(),
            $descriptor->getHeaders(),
            $descriptor->fetchBody()
        );
    }
}
