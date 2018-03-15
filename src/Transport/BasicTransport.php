<?php

namespace Fei\ApiClient\Transport;

use Fei\ApiClient\ApiClientException;
use Fei\ApiClient\RequestDescriptor;
use Fei\ApiClient\ResponseDescriptor;
use Fei\ApiClient\Transport\Psr7\RequestFactory;
use GuzzleHttp\Client;

/**
 * Class BasicTransport
 *
 * @package Fei\ApiClient\Transport
 */
class BasicTransport implements SyncTransportInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var array
     */
    protected $clientOptions = array();

    /**
     * BasicTransport constructor.
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->clientOptions = $options;
        $this->requestFactory = new RequestFactory();
    }


    /**
     * @param RequestDescriptor $requestDescriptor
     *
     * @param int               $flags
     *
     * @return ResponseDescriptor
     * @throws ApiClientException
     * @throws \Exception
     */
    public function send(RequestDescriptor $requestDescriptor, $flags = 0)
    {
        try {
            $request = $this->getRequestFactory()->create($requestDescriptor);
            $response = $this->getClient()->send($request);
        } catch (\Exception $exception) {
            throw new ApiClientException('An error occurred while transporting a request', $exception->getCode(), $exception);
        }

        $responseDescriptor = new ResponseDescriptor();
        $responseDescriptor->setBody($response->getBody());
        $responseDescriptor->setCode($response->getStatusCode());
        $responseDescriptor->setHeaders($response->getHeaders());

        return $responseDescriptor;
    }

    public function sendMany(array $requestDescriptors)
    {
        try {
            $requests = [];
            foreach ($requestDescriptors as $requestDescriptor) {
                list($request, $params) = $requestDescriptor;
                if (!$request instanceof RequestDescriptor) {
                    throw new ApiClientException('Invalid parameter. sendMany only accept array of RequestDescriptor.');
                }
                $requests[] = $this->getClient()->sendAsync($this->getRequestFactory()->create($request));
            }

            return \GuzzleHttp\Promise\unwrap($requests);
        } catch (\Exception $exception) {
            throw new ApiClientException('An error occurred while transporting a request', $exception->getCode(), $exception);
        }
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new Client($this->clientOptions);
        }

        return $this->client;
    }

    /**
     * @param Client $client
     *
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get RequestFactory
     *
     * @return RequestFactory
     */
    public function getRequestFactory()
    {
        return $this->requestFactory;
    }

    /**
     * Set RequestFactory
     *
     * @param RequestFactory $requestFactory
     *
     * @return $this
     */
    public function setRequestFactory(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;

        return $this;
    }
}
