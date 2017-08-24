<?php

    namespace Fei\ApiClient;

    use Fei\ApiClient\Transport\AsyncTransportInterface;
    use Fei\ApiClient\Transport\BasicTransport;
    use Fei\ApiClient\Transport\SyncTransportInterface;
    use Fei\ApiClient\Transport\TransportException;
    use Fei\ApiClient\Transport\TransportInterface;
    use Fei\Entity\EntityInterface;
    use Fei\Service\Connect\Client\Token;

    /**
     * Class AbstractApiApiClient
     *
     * @package Fei\ApiClient
     */
    abstract class AbstractApiClient implements ApiClientInterface
    {

        /**
         * Remote service base URL (without path)
         */
        const OPTION_BASEURL = 'baseUrl';

        const OPTION_APPLICATION_ID = 'applicationId';

        const OPTION_PRIVATE_KEY = 'privateKey';

        const OPTION_CONNECT_URL = 'connectUrl';

        /**
         * @var string
         */
        protected $baseUrl;

        /**
         * @var  SyncTransportInterface
         */
        protected $transport;

        /**
         * @var AsyncTransportInterface
         */
        protected $asyncTransport;

        /**
         * @var TransportInterface;
         */
        protected $fallbackTransport;

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
         * @var bool
         */
        protected $autoCommit = true;

        /**
         * @var bool Ignore delay settings for next request
         */
        protected $forceNext = false;

        /**
         * @var array
         */
        protected $options = array();

        /**
         * @var array
         */
        protected $availableOptions = array();

        /**
         * Connect Token Client
         *
         * @var Token
         */
        protected $tokenClient;

        /**
         * AbstractApiClient constructor.
         *
         * @param array $options
         * @param Token|null $tokenClient
         */
        public function __construct(array $options = array(), Token $tokenClient = null)
        {
            $this->initOptions();
            $this->setOptions($options);
            $this->setTokenClient($tokenClient);
        }

        /**
         * Initialize availableOptions property based on existing OPTION_* constants
         */
        protected function initOptions()
        {
            $reflectedClient = new \ReflectionObject($this);
            $constants       = $reflectedClient->getConstants();

            foreach ($constants as $constant => $value)
            {
                if(strpos($constant, 'OPTION_') === 0)
                {
                    $this->availableOptions[] = $value;
                }
            }
        }

        /**
         * @param array $options
         *
         * @return $this
         */
        public function setOptions(array $options)
        {
            foreach ($options as $option => $value)
            {
                $this->setOption($option, $value);
            }

            return $this;
        }

        /**
         * @param string $option
         * @param mixed  $value
         *
         * @return $this
         */
        public function setOption($option, $value)
        {
            if(in_array($option, $this->availableOptions))
            {
                $method = sprintf('set%s', ucfirst($option));
                if (method_exists($this, $method)) {
                    $this->$method($value);
                } else {
                    $this->$option = $value;
                }

                return $this;
            }

            throw new ApiClientException(sprintf('Trying to set unknown option "%s" on %s ', $option, get_class($this)));

        }



        /**
         * @param string $option
         * @param mixed  $default
         *
         * @return $this
         */
        public function getOption($option, $default = null)
        {
            if(in_array($option, $this->availableOptions))
            {
                $method = 'get' . ucfirst($option);
                if (method_exists($this, $method)) {
                    return $this->$method();
                } elseif(!empty($this->$option)) {
                    return $this->$option;
                }

                return $default;
            }

            else throw new ApiClientException(sprintf('Trying to get unknown option "%s" on %s ', $option, get_class($this)));

        }

        /**
         * @return $this
         */
        public function enableAutoCommit()
        {
            $this->begin();

            $instance = $this;
            register_shutdown_function(function () use ($instance)
            {
                if ($instance->getTransport() && !empty($instance->delayedRequests) && $instance->autoCommit)
                {
                    $instance->commit();
                }
            });

            $this->autoCommit = true;

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

            // also reset forceNext flag since both modes are not compatible
            $this->forceNext = false;

            return $this;
        }

        /**
         * @param RequestDescriptor $request
         * @param int               $flags
         *
         * @return EntityInterface
         */
        public function fetch(RequestDescriptor $request, $flags = 0)
        {
            $response = $this->send($request, $flags);
            $class    = $response->getMeta('entity');

            $data   = $response->getData();
            $entity = array();

            if (!empty($class))
            {

                if (isset($data[0]) && is_array($data[0]))
                {
                    foreach ($data as $resource)
                    {
                        /** @var EntityInterface $entity */
                        $subEntity = new $class;
                        $entity->hydrate($resource);
                        $entity[] = $subEntity;
                    }
                }
                else
                {

                    /** @var EntityInterface $entity */
                    $entity = new $class;
                    $entity->hydrate($response->getData());
                }
            }

            return $entity;
        }

        /**
         * @return TransportInterface
         */
        public function getTransport()
        {
            return $this->transport;
        }

        /**
         * @param SyncTransportInterface $transport
         *
         * @return $this
         */
        public function setTransport(SyncTransportInterface $transport)
        {
            $this->transport = $transport;

            return $this;
        }

        /**
         * @return AsyncTransportInterface
         */
        public function getAsyncTransport()
        {
            return $this->asyncTransport;
        }

        /**
         * @param AsyncTransportInterface $asyncTransport
         *
         * @return $this
         */
        public function setAsyncTransport(AsyncTransportInterface $asyncTransport)
        {
            $this->asyncTransport = $asyncTransport;

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
         * @return ResponseDescriptor|bool
         */
        public function send(RequestDescriptor $request, $flags = 0)
        {
            if ($this->delayNext || $this->isDelayed)
            {
                if(!$this->forceNext)
                {
                    $this->delayedRequests[] = array($request, $flags | ApiRequestOption::NO_RESPONSE);

                    // reset stackNext flag
                    $this->delayNext = false;

                    return true;
                }
                else {
                    // reset forceNext flag
                    $this->forceNext = false;
                }
            }

            $transport = $this->getAppropriateTransport($flags);

            if (is_null($transport)) {
                throw new ApiClientException(sprintf('No transport has been set on "%s"', get_class()));
            }

            // want to add the token in the request headers
            if ($flags & ApiRequestOption::SAFE_MODE) {
                $tokenClient = $this->getTokenClient();

                if (!$tokenClient instanceof Token) {
                    throw new ApiClientException('You need to set the Token Client when using the safe mode flag!');
                }

                $applicationId = $tokenClient->getApplicationId();
                if ($applicationId === null) {
                    throw new ApiClientException('The identifier of the application is not set. You have to set it before using the safe mode.');
                }

                $privateKey = $tokenClient->getPrivateKey();
                if ($privateKey === null) {
                    throw new ApiClientException('The private key of the application is not set. You have to set it before using the safe mode.');
                }

                $token = $tokenClient->createApplicationToken($applicationId, $privateKey);
                $request->addHeader('connect-token', $token);
                $request->addHeader('connect-application', $applicationId);
            }

            try
            {
                $response = $transport->send($request, $flags);
            }
            catch(TransportException $e)
            {
                // fallback?
                if($fallback = $this->getFallbackTransport())
                {
                    $response = $fallback->send($request, $flags);
                }
                else {
                    throw $e;
                }
            }

            $this->resetFallbackTransport();

            return $response;
        }


        /**
         * Returns the most appropriate transport according to request flags
         *
         * @param $flags
         *
         * @return AsyncTransportInterface|TransportInterface
         */
        protected function getAppropriateTransport($flags)
        {
            if ($flags & ApiRequestOption::NO_RESPONSE)
            {
                $transport = $this->getAsyncTransport();
                if (is_null($transport))
                {
                    $transport = $this->getTransport();
                }
                else {
                    $fallback = $this->getTransport();
                    if($fallback)
                    {
                        $this->setFallbackTransport($fallback);
                    }
                }
            }
            else
            {
                $transport = $this->getTransport();
            }

            return $transport;
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
        public function commit()
        {
            $this->isDelayed = false;

            if (!empty($this->delayedRequests))
            {
                $this->sendMany($this->delayedRequests);
            }

            $this->delayedRequests = array();

            return $this;
        }

        /**
         * @param array $requests
         *
         * @return mixed
         */
        public function sendMany(array $requests)
        {
            $response = $this->getTransport()->sendMany($requests);

            return $response;
        }

        /**
         * @return $this
         */
        public function disableAutoCommit()
        {
            $this->autoCommit = false;

            return $this;
        }

        /**
         * @return $this
         */
        public function force()
        {
            $this->forceNext = true;

            // also reset the delayNext flag since both are not compatible
            $this->delayNext = false;

            return $this;
        }

        /**
         * @return TransportInterface
         */
        public function getFallbackTransport()
        {
            return $this->fallbackTransport;
        }

        /**
         * @param TransportInterface $fallbackTransport
         *
         * @return $this
         */
        public function setFallbackTransport(TransportInterface $fallbackTransport)
        {
            $this->fallbackTransport = $fallbackTransport;

            return $this;
        }

        /**
         *
         */
        public function resetFallbackTransport()
        {
            $this->fallbackTransport = null;

            return $this;
        }

        /**
         * Get TokenClient
         *
         * @return Token
         */
        public function getTokenClient()
        {
            return $this->tokenClient;
        }

        /**
         * Set TokenClient
         *
         * @param Token $tokenClient
         *
         * @return $this
         */
        public function setTokenClient($tokenClient)
        {
            if (null === $tokenClient && get_called_class() !== Token::class) {
                $tokenClient = new Token([self::OPTION_BASEURL => $this->getOption(self::OPTION_CONNECT_URL)]);
                $tokenClient->setApplicationId($this->getOption(self::OPTION_APPLICATION_ID));
                $tokenClient->setPrivateKey($this->getOption(self::OPTION_PRIVATE_KEY));
                $tokenClient->setTransport(new BasicTransport());
            }

            $this->tokenClient = $tokenClient;

            return $this;
        }

    }
