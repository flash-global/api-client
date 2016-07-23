<?php
    
    namespace Fei\ApiClient\Transport;

    /**
     * Class AsyncTransportInterface
     *
     * Async Transport is exactly the same as Transport,
     * but this interface allows to filter transports that
     * can be set as asynchronous transports
     *
     * @package Fei\ApiClient\Transport
     */
    interface AsyncTransportInterface extends TransportInterface
    {
        
    }
