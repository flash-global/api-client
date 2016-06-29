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
         * @return Response
         */
        public function send(RequestDescriptor $requestDescriptor, $flags = 0);


    }
