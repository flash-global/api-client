<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/06/2016
 * Time: 10:31
 */

namespace Fei\ApiClient\Transport;


use Amp\Artax\Client as AmpClient;
use Amp\Artax\Request as AmpRequest;
use Amp\Promise;
use Fei\ApiClient\ApiClientException;
use Fei\ApiClient\Request;

/**
 * Class AsyncTransport
 * @package Fei\ApiClient\Transport
 */
class AsyncTransport implements TransportInterface
{

    /**
     * AsyncTransport constructor.
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        /** @var Client $client */
        $this->client = new AmpClient();
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
        $request = new AmpRequest();
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
        $request = new AmpRequest();
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
     * @throws ApiClientException
     */
    public function sendMany($data, $to = null, $headers = array())
    {
        foreach ($data as $request) {
            if (!$request instanceof AmpRequest) {
                throw new ApiClientException(sprintf("%s is not an instance of %s. It can't be sent.", get_class($request),
                    'Amp\Artax\Request'));
            }
        }

        if (!empty($to)) {
            $requests = array();
            foreach ($data as $request) {
                if ($request instanceof AmpRequest) {
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
     * @param $request
     *
     * @return Promise
     * @throws ApiClientException
     */
    public function send(Request $request)
    {
        if (!$request instanceof Request) {
            throw new ApiClientException(sprintf('AsyncTransport needs an %s instance. Instance of %s given.',
                'Amp\Artax\Request', get_class($request)));
        }

        // No need to try catch here, Client::request() method itself will never throw
        return $this->client->request($request);
    }
}
