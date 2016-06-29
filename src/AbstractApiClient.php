<?php
    /**
     * Created by PhpStorm.
     * User: Neofox
     * Date: 21/06/2016
     * Time: 10:29
     */

    namespace Fei\ApiClient;


    use Fei\ApiClient\Transport\TransportInterface;

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
        protected $stackNext;

        /**
         * @var array
         */
        protected $stackedRequests;

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
        public function stack()
        {
            $this->stackNext = true;
        }

        /**
         * @param RequestDescriptor $request
         *
         * @return Transport\Response
         */
        public function send(RequestDescriptor $request)
        {
            if($this->stackNext)
            {
                $this->stackedRequests[] = $request;

                // reset stackNext flag
                $this->stackNext = false;
    
                return null;
            }

            $response = $this->getTransport()->send($request);

            return $response;
        }

    }
