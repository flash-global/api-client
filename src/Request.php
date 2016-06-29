<?php
    
    namespace Fei\ApiClient;
    
    
    class Request
    {
        protected $url;

        protected $method;

        protected $params;

        protected $body;

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
         * @return mixed
         */
        public function getParams()
        {
            return $this->params;
        }

        /**
         * @param mixed $params
         *
         * @return $this
         */
        public function setParams($params)
        {
            $this->params = $params;

            return $this;
        }

        /**
         * @return mixed
         */
        public function getBody()
        {
            return $this->body;
        }

        /**
         * @param mixed $body
         *
         * @return $this
         */
        public function setBody($body)
        {
            $this->body = $body;

            return $this;
        }

        
    }
