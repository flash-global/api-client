<?php
    
    namespace Fei\ApiClient\Transport;

    /**
     * Class SyncTransportInterface
     *
     * Sync Transport is exactly the same as Transport,
     * but this interface allows to filter transports that
     * can be set as synchronous transports
     *
     * @package Fei\ApiClient\Transport
     */
    interface SyncTransportInterface extends TransportInterface
    {
        
    }
