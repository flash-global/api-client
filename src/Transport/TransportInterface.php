<?php
    /**
     * Created by PhpStorm.
     * User: Neofox
     * Date: 21/06/2016
     * Time: 10:31
     */

    namespace Fei\ApiClient\Transport;

    use Fei\ApiClient\Request;


    /**
     * Interface TransportInterface
     *
     * @package Fei\ApiClient\Transport
     */
    interface TransportInterface
    {
        /**
         * @param $request
         *
         * @return Response
         */
        public function send(Request $request);


    }
