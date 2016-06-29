<?php
    /**
     * Created by PhpStorm.
     * User: Neofox
     * Date: 21/06/2016
     * Time: 10:31
     */

    namespace Fei\ApiClient\Transport;


    use Fei\ApiClient\RequestDescriptor;

    use Guzzle\Http\Message\RequestInterface;

    use Guzzle\Service\Client;
    use Fei\ApiClient\ApiClientException;

    /**
     * Class BasicTransport
     *
     * @package Fei\ApiClient\Transport
     */
    class BasicTransport implements TransportInterface
    {

        protected $client;

        /**
         * BasicTransport constructor.
         *
         * @param array $options
         */
        public function __construct($options = [])
        {
            /** @var Client client */
            $this->client = new Client();
            $this->client->options(null, $options);
        }

        /**
         * @return Client
         */
        public function getClient()
        {
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
         * @param       $data
         * @param       $to
         * @param array $headers
         *
         * @return \Guzzle\Http\Message\Request|RequestInterface
         * @throws \Exception
         */
        public function post($data, $to, $headers = [])
        {
            $request = $this->client->createRequest('POST', $to, $headers, $data);

            return $request;
        }

        /**
         * @param       $from
         * @param array $headers
         *
         * @return \Guzzle\Http\Message\Request|RequestInterface
         * @throws \Exception
         */
        public function get($from, $headers = [])
        {
            $request = $this->client->createRequest('GET', $from, $headers);

            return $request;
        }

        /**
         * @param array $data
         * @param       $to
         * @param array $headers
         *
         * @return mixed
         * @throws \Exception
         */
        public function sendMany($data, $to = null, $headers = [])
        {
            foreach ($data as $request)
            {
                if (!$request instanceof RequestInterface)
                {
                    throw new ApiClientException("data must be an array of RequestInterface.");
                }
            }

            return $this->send($data);
        }

        /**
         * @param RequestDescriptor $request
         *
         * @return Response
         * @throws \Exception
         */
        public function send(RequestDescriptor $request)
        {
            if ((!$request instanceof RequestDescriptor) && (!is_array($request)))
            {
                throw new ApiClientException(sprintf('BasicTransport needs an %s object. Instance of %s given.',
                    '\Guzzle\Http\Message\Request', get_class($request)));
            }
            try
            {
                $response = $this->client->send($request);

            } catch (\Exception $exception)
            {
                throw $exception;
            }

            return $response;
        }
    }
