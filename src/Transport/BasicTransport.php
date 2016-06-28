<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/06/2016
 * Time: 10:31
 */

namespace Pricer\WebClient\Transport;


use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Service\Client;
use Pricer\WebClient\WebClientException;

/**
 * Class BasicTransport
 * @package Pricer\WebClient\Transport
 */
class BasicTransport extends AbstractTransport
{

    /**
     * BasicTransport constructor.
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        /** @var Client client */
        $this->client = new Client();
        $this->client->options(null, $options);
    }

    /**
     * @param       $data
     * @param       $to
     * @param array $headers
     *
     * @return \Guzzle\Http\Message\Request|RequestInterface
     * @throws \Exception
     */
    public function post($data, $to, $headers = array())
    {
        $request = $this->client->createRequest('POST', $to, $headers, $data);

        return $request;
    }

    /**
     * @param       $from
     * @param array $headers
     *
     * @return \Guzzle\Http\Message\Request|RequestInterface
     * @throws \Exception
     */
    public function get($from, $headers = array())
    {
        $request = $this->client->createRequest('GET', $from, $headers);

        return $request;
    }

    /**
     * @param array $data
     * @param       $to
     * @param array $headers
     *
     * @return mixed
     * @throws \Exception
     */
    public function sendMany($data, $to = null, $headers = array())
    {
        foreach ($data as $request) {
            if (!$request instanceof RequestInterface) {
                throw new WebClientException("data must be an array of RequestInterface.");
            }
        }

        return $this->send($data);
    }

    /**
     * @param \Guzzle\Http\Message\Request $data
     *
     * @return Response|null
     * @throws \Exception
     */
    public function send($data)
    {
        if ((!$data instanceof Request) && (!is_array($data))) {
            throw new WebClientException(sprintf('BasicTransport needs an %s object. Instance of %s given.',
                '\Guzzle\Http\Message\Request', get_class($data)));
        }
        try {
            $response = $this->client->send($data);

        } catch (\Exception $exception) {
            throw $exception;
        }

        return $response;
    }
}