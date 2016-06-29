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
         * Tells client to stack the next request
         */
        public function delay()
        {
            $this->delayNext = true;

            return $this;
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
        public function rollback()
        {
            $this->isDelayed       = false;
            $this->delayedRequests = array();

            return $this;
        }

        /**
         * @param RequestDescriptor $request
         *
         * @param int               $flags
         *
         * @return Transport\Response
         */
        public function send(RequestDescriptor $request, $flags = 0)
        {
            if ($this->delayNext || $this->isDelayed)
            {
                $this->delayedRequests[] = [$request, $flags | ApiRequestOption::NO_RESPONSE];

                // reset stackNext flag
                $this->delayNext = false;

                return null;
            }

            $response = $this->getTransport()->send($request, $flags);

            return $response;
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
         * @return $this
         */
        public function begin()
        {
            $this->isDelayed = true;

            return $this;
        }

        /**
         *
         */
        public function commit()
        {
            $this->isDelayed = false;

            while($params = array_shift($this->delayedRequests))
            {
                list($request, $flags) = $params;
                $this->send($request, $flags);
            }

            return $this;
        }

    }
