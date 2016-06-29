<?php
    
    namespace Fei\ApiClient;
    
    
    class RequestDescriptor
    {
        protected $url;
        
        protected $method;
        
        protected $params = array();
        
        protected $bodyParams = array();

        protected $headers = array();

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
        public function addParam(string $key, string $value)
        {
            $this->params[$key] = $value;
        }

        /**
         * @param string $key
         * @return mixed
         */
        public function getParam(string $key)
        {
            return $this->params[$key];
        }

        /**
         * @param string $key
         * @param string $value
         */
        public function addBodyParam(string $key, string $value)
        {
            $this->bodyParams[$key] = $value;
        }

        /**
         * @param string $key
         * @return mixed
         */
        public function getBodyParam(string $key)
        {
            return $this->bodyParams[$key];
        }

        /**
         * @param string $key
         * @param string $value
         */
        public function addHeader(string $key, string $value)
        {
            $this->headers[$key] = $value;
        }

        /**
         * @param string $key
         * @return mixed
         */
        public function getHeader(string $key)
        {
            return $this->headers[$key];
        }

    }
