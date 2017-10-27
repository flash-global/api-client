<?php
    
    namespace Fei\ApiClient\Transport;
    
    
    use Fei\ApiClient\ApiClientException;
    use Fei\ApiClient\RequestDescriptor;
    use Fei\ApiClient\ResponseDescriptor;
    use Guzzle\Service\Client;
    
    /**
     * Class BasicTransport
     *
     * @package Fei\ApiClient\Transport
     */
    class BasicTransport implements SyncTransportInterface
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
         * @param RequestDescriptor $requestDescriptor
         *
         * @param int               $flags
         *
         * @return ResponseDescriptor
         * @throws ApiClientException
         * @throws \Exception
         */
        public function send(RequestDescriptor $requestDescriptor, $flags = 0)
        {
            try
            {
                $request  = $this->getClient()
                                 ->createRequest($requestDescriptor->getMethod(), $requestDescriptor->getUrl(), $requestDescriptor->getHeaders(), $requestDescriptor->getBodyParams())
                ;
                $response = $this->getClient()->send($request);
                
            } catch (\Exception $exception)
            {
                throw new ApiClientException('An error occurred while transporting a request: ' . $exception->getMessage(), $exception->getCode(), $exception);
            }
            
            $responseDescriptor = new ResponseDescriptor();
            $responseDescriptor->setBody($response->getBody());
            $responseDescriptor->setCode($response->getStatusCode());
            $responseDescriptor->setHeaders($response->getHeaders());
            
            return $responseDescriptor;
        }
        
        public function sendMany(array $requestDescriptors)
        {
            try
            {
                $requests = array();
                
                foreach ($requestDescriptors as $requestDescriptor)
                {
                    list($request, $params) = $requestDescriptor;
                    
                    if (!$request instanceof RequestDescriptor)
                    {
                        throw new ApiClientException('Invalid parameter. sendMany only accept array of RequestDescriptor.');
                    }
                    
                    $requests[] = $this->getClient()->createRequest($request->getMethod(), $request->getUrl(),
                        $request->getHeaders(), $request->getBodyParams())
                    ;
                }
                
                $this->getClient()->send($requests);
            } catch (\Exception $exception)
            {
                throw new ApiClientException('An error occurred while transporting a request: ' . $exception->getMessage(), $exception->getCode(), $exception);
            }
        }
        
        /**
         * @return Client
         */
        public function getClient()
        {
            if (is_null($this->client))
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
    }
