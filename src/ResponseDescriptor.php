<?php
    
    
    namespace Fei\ApiClient;
    
    
    class ResponseDescriptor
    {
        protected $code;
        protected $headers = array();
        protected $body;

        /**
         * @return mixed
         */
        public function getCode()
        {
            return $this->code;
        }

        /**
         * @param mixed $code
         */
        public function setCode($code)
        {

                $this->code = (int)$code;
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
         */
        public function setHeaders($headers)
        {
            $this->headers = $headers;
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
         */
        public function setBody($body)
        {
            $this->body = $body;
        }

        
    }
