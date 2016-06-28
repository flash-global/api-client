<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/06/2016
 * Time: 10:31
 */

namespace Pricer\WebClient\Transport;


use Amp\Artax\Client;
use Amp\Artax\Request;
use Amp\Promise;
use Pricer\WebClient\WebClientException;

/**
 * Class AsyncTransport
 * @package Pricer\WebClient\Transport
 */
class AsyncTransport extends AbstractTransport
{

    /**
     * AsyncTransport constructor.
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        /** @var Client $client */
        $this->client = new Client();
        $this->client->setAllOptions($options);
    }

    /**
     * @param       $data
     * @param       $to
     * @param array $headers
     *
     * @return Request
     */
    public function post($data, $to, $headers = array())
    {
        $request = new Request();
        $request
            ->setUri($to)
            ->setMethod('POST')
            ->setBody($data)
            ->setAllHeaders($headers);

        return $request;
    }

    /**
     * @param       $from
     * @param array $headers
     *
     * @return Request
     */
    public function get($from, $headers = array())
    {
        $request = new Request();
        $request
            ->setUri($from)
            ->setMethod('GET')
            ->setAllHeaders($headers);

        return $request;
    }

    /**
     * @param       $data
     * @param       $to
     * @param array $headers
     *
     * @return array[\Amp\Promise]
     * @throws WebClientException
     */
    public function sendMany($data, $to = null, $headers = array())
    {
        foreach ($data as $request) {
            if (!$request instanceof Request) {
                throw new WebClientException(sprintf("%s is not an instance of %s. It can't be send.", get_class($request),
                    'Amp\Artax\Request'));
            }
        }

        if (!empty($to)) {
            $requests = array();
            foreach ($data as $request) {
                if ($request instanceof Request) {
                    $requests[] = $request->setUri($to);
                }
            }
        } else {
            $requests = $data;
        }

        // No need to try catch here, Client::request() method itself will never throw
        return $this->client->requestMulti($requests);
    }

    /**
     * @param $data
     *
     * @return Promise
     * @throws WebClientException
     */
    public function send($data)
    {
        if (!$data instanceof Request) {
            throw new WebClientException(sprintf('AsyncTransport needs an %s object. Instance of %s given.',
                'Amp\Artax\Request', get_class($data)));
        }


        // No need to try catch here, Client::request() method itself will never throw
        return $this->client->request($data);
    }
}