<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/06/2016
 * Time: 10:29
 */

namespace Fei\ApiClient;

use Fei\ApiClient\Transport\TransportInterface;


/**
 * Interface ApiClientInterface
 * @package Fei\ApiClient
 */
interface ApiClientInterface
{
    public function getTransport();
    
    public function setTransport(TransportInterface $transport);
}
