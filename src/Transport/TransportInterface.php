<?php
    /**
     * Created by PhpStorm.
     * User: Neofox
     * Date: 21/06/2016
     * Time: 10:31
     */

    namespace Fei\ApiClient\Transport;

    use Fei\ApiClient\RequestDescriptor;
    use Fei\ApiClient\Response;
    use Fei\ApiClient\ResponseDescriptor;


    /**
     * Interface TransportInterface
     *
     * @package Fei\ApiClient\Transport
     */
    interface TransportInterface
    {
        /**
         * @param RequestDescriptor $requestDescriptor
         * @param int               $flags     Options binary flags
         *
         * @return ResponseDescriptor
         */
        public function send(RequestDescriptor $requestDescriptor, $flags = 0);

        /**
         * @param array $requests
         *
         * @return mixed
         */
        public function sendMany(array $requests);

    }
