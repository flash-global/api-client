<?php

    namespace Fei\ApiClient\Transport;


    use Fei\ApiClient\RequestDescriptor;
    use Fei\ApiClient\Response;
    use Fei\ApiClient\ResponseDescriptor;
    use Guzzle\Service\Client;
    use Fei\ApiClient\ApiClientException;

    /**
     * Class BasicTransport
     *
     * @package Fei\ApiClient\Transport
     */
    class BasicTransport implements TransportInterface
    {
        /**
         * @var Client
         */
        protected $client;

        protected $clientOptions = array();

        /**
         * BasicTransport constructor.
         *
         * @param array $options
         */
        public function __construct($options = array())
        {
            $this->clientOptions = $options;
        }

        /**
         * @return Client
         */
        public function getClient()
        {
            if(is_null($this->client))
            {
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
            try
            {
                $request = $this->getClient()->createRequest($requestDescriptor->getMethod(), $requestDescriptor->getUrl(), $requestDescriptor->getHeaders(), $requestDescriptor->getBodyParams());
                $response = $this->getClient()->send($request);

            } catch (\Exception $exception)
            {
                throw new ApiClientException('An error occurred while transporting a request', $exception->getCode(), $exception);
            }

            $responseDescriptor = new ResponseDescriptor();
            $responseDescriptor->setBody($response->getBody());
            $responseDescriptor->setCode($response->getStatusCode());
            $responseDescriptor->setHeaders($response->getHeaders());
            
            return $responseDescriptor;
        }
    }
