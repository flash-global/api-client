<?php

    namespace Fei\ApiClient;


    class RequestDescriptor
    {
        protected $url;

        protected $method;

        protected $params = array();

        protected $bodyParams = array();

        protected $headers = array();

        public function __construct($data = null)
        {
            if(!is_null($data))
            {
                $this->hydrate($data);
            }
        }

        /**
         * @param $data
         *
         * @return $this
         * @throws ApiClientException
         */
        public function hydrate($data)
        {
            if($data instanceof \ArrayObject)
            {
                $data = $data->getArrayCopy();
            }

            if($data instanceof \Iterator)
            {
                $data = iterator_to_array($data);
            }

            if(!is_array($data)) {
                throw new ApiClientException('RequestDescriptor need an array, ArrayObject or Iterator instance to get hydrated');
            }

            foreach($data as $property => $value)
            {
                $setter = 'set' . ucfirst($property);

                $this->$setter($value);
            }

            return $this;
        }

        /**
         * @return array
         */
        public function getBodyParams()
        {
            return $this->bodyParams;
        }

        /**
         * @param array $bodyParams
         */
        public function setBodyParams($bodyParams)
        {
            $this->bodyParams = $bodyParams;
        }

        /**
         * @return mixed
         */
        public function getUrl()
        {
            return $this->url;
        }

        /**
         * @param mixed $url
         *
         * @return $this
         */
        public function setUrl($url)
        {
            $this->url = $url;

            return $this;
        }

        /**
         * @return mixed
         */
        public function getMethod()
        {
            return $this->method;
        }

        /**
         * @param mixed $method
         *
         * @return $this
         */
        public function setMethod($method)
        {
            $this->method = $method;

            return $this;
        }


        /**
         * @return array
         */
        public function getParams()
        {
            return $this->params;
        }

        /**
         * @param array $params
         *
         * @return $this
         */
        public function setParams($params)
        {
            $this->params = $params;

            return $this;
        }

        /**
         * @return array
         */
        public function getHeaders()
        {
            return $this->headers;
        }

        /**
         * @param array $headers
         *
         * @return $this
         */
        public function setHeaders($headers)
        {
            $this->headers = $headers;

            return $this;
        }

        /**
         * @param string $key
         * @param string $value
         */
        public function addParam($key, $value)
        {
            $this->params[$key] = $value;
        }

        /**
         * @param string $key
         * @return mixed
         */
        public function getParam($key)
        {
            return $this->params[$key];
        }

        /**
         * @param string $key
         * @param string $value
         */
        public function addBodyParam($key, $value)
        {
            $this->bodyParams[$key] = $value;
        }

        /**
         * @param string $key
         * @return mixed
         */
        public function getBodyParam($key)
        {
            return $this->bodyParams[$key];
        }

        /**
         * @param string $key
         * @param string $value
         */
        public function addHeader($key, $value)
        {
            $this->headers[$key] = $value;
        }

        /**
         * @param string $key
         * @return mixed
         */
        public function getHeader($key)
        {
            return $this->headers[$key];
        }

        /**
         * @return array
         */
        public function toArray()
        {
            return get_object_vars($this);
        }
    }
