<?php
    
    
    namespace Fei\ApiClient;
    
    
    class ResponseDescriptor implements \JsonSerializable
    {
        
        /** @var  int */
        protected $code;
        
        /** @var array */
        protected $headers = array();
        
        /** @var  string */
        protected $body;
        
        /** @var  string */
        protected $data;
        
        /** @var  array */
        protected $meta;
        
        /** @var  string */
        protected $metaEntity;
        
        /**
         * @return int
         */
        public function getCode()
        {
            return $this->code;
        }
        
        /**
         * @param int $code
         *
         * @return ResponseDescriptor
         */
        public function setCode($code)
        {
            $this->code = (int) $code;
            
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
         * @return ResponseDescriptor
         */
        public function setHeaders($headers)
        {
            $this->headers = $headers;
            
            return $this;
        }
        
        /**
         * @return string
         */
        public function getBody()
        {
            return $this->body;
        }
        
        /**
         * @param string $body
         *
         * @return ResponseDescriptor
         */
        public function setBody($body)
        {
            $this->body = $body;
            
            return $this;
        }
        
        /**
         * @return array
         */
        public function getData()
        {
            $data = '';
            $body = json_decode($this->body, true);
            
            if (!empty($body) && isset($body['data']))
            {
                $data = $body['data'];
            }
            
            return $data;
        }
        
        /**
         * @param null|string $key
         *
         * @return array
         */
        public function getMeta($key = null)
        {
            $meta = '';
            $body = json_decode($this->body, true);
            
            if (!empty($body) && isset($body['meta']))
            {
                
                if (!empty($key) && isset($body['meta'][$key]))
                {
                    $meta = $body['meta'][$key];
                }
                else
                {
                    $meta = $body['meta'];
                }
            }
            
            return $meta;
        }
    
        function jsonSerialize()
        {
            return get_object_vars($this);
        }
    
    }
