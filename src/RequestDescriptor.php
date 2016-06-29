<?php
    
    namespace Fei\ApiClient;
    
    
    class RequestDescriptor
    {
        protected $url;
        
        protected $method;
        
        protected $params  = [];
        
        protected $body    = [];
        
        protected $headers = [];
        
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
        
        
    }
