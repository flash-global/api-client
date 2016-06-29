<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/06/2016
 * Time: 10:31
 */

namespace Fei\ApiClient\Transport;


use Amp\Promise;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;

/**
 * Interface TransportInterface
 * @package Fei\ApiClient\Transport
 */
interface TransportInterface
{
    /**
     * @param        $data
     * @param string $to
     * @param array  $headers
     *
     * @return Request|\Amp\Artax\Request
     */
    public function post($data, $to, $headers = array());

    /**
     * @param string $from
     * @param array  $headers
     *
     * @return Request|\Amp\Artax\Request
     */
    public function get($from, $headers = array());

    /**
     * @param       $data
     * @param       $to
     * @param array $headers
     *
     * @return array[\Amp\Promise]
     */
    public function sendMany($data, $to = null, $headers = array());

    /**
     * @param $data
     *
     * @return Promise|Response
     */
    public function send($data);


}
