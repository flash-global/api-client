<?php
    /**
     * Created by PhpStorm.
     * User: Neofox
     * Date: 21/06/2016
     * Time: 10:31
     */

    namespace Fei\ApiClient\Transport;


    use Fei\ApiClient\RequestDescriptor;
    use Fei\ApiClient\Response;
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
         * @return Response
         * @throws ApiClientException
         * @throws \Exception
         */
        public function send(RequestDescriptor $requestDescriptor, $flags = 0)
        {
            if (!$requestDescriptor instanceof RequestDescriptor)
            {
                throw new ApiClientException(sprintf('BasicTransport expects a RequestDescriptor object. Instance of %s given.', get_class($requestDescriptor)));
            }
            try
            {
                $request = $this->client->createRequest($requestDescriptor->getMethod(), $requestDescriptor->getUrl(), $requestDescriptor->getHeaders());
                $response = $this->client->send($request);

            } catch (\Exception $exception)
            {
                throw new ApiClientException('An error occurred while transporting a request', $exception->getCode(), $exception);
            }

            return $response;
        }
    }
