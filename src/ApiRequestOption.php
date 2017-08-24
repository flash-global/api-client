<?php
    /**
     * This file is part of the Objective PHP project
     *
     * More info about Objective PHP on www.objective-php.org
     *
     * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
     */
    
    namespace Fei\ApiClient;

    /**
     * Class ApiRequestOption
     *
     * Constant
     *
     * @package Fei\ApiClient
     */
    class ApiRequestOption
    {
        /**
         * Tells the transport that a repsonse is not expected nor awaited
         */
        const NO_RESPONSE = 1;

        /**
         * Tells whether or not the call is in safe mode (add a token in the header
         */
        const SAFE_MODE = 2;
    }
