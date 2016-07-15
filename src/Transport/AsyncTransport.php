<?php

namespace Fei\ApiClient\Transport;


use Amp\Artax\Client as AmpClient;
use Amp\Artax\FormBody;
use Amp\Artax\Request as AmpRequest;
use Amp\Artax\Request;
use Amp\Artax\Response;
use Amp\Promise;
use Fei\ApiClient\ApiClientException;
use Fei\ApiClient\ApiRequestOption;
use Fei\ApiClient\RequestDescriptor;
use Fei\ApiClient\ResponseDescriptor;
use Fei\Entity\Exception;

/**
 * Class AsyncTransport
 * @package Fei\ApiClient\Transport
 */
class AsyncTransport implements TransportInterface
{
    /** @var array  */
    protected $clientOptions = array();

    /** @var  AmpClient */
    protected $client;

    /**
     * @var array
     */
    protected $promises = array();

    /**
     * BasicTransport constructor.
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->clientOptions = $options;
    }

    /**
     * @return AmpClient
     */
    public function getClient()
    {
        if(is_null($this->client))
        {
            $this->client = new AmpClient();
            $this->client->setAllOptions($this->clientOptions);
        }

        return $this->client;
    }

    /**
     * @param AmpClient $client
     *
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    public function sendMany(array $requestDescriptors)
    {
        $requests = array();

        foreach ($requestDescriptors as $requestDescriptor)
        {
            list($request, $params) = $requestDescriptor;

            if(!$request instanceof RequestDescriptor)
            {
                throw new ApiClientException('Invalid parameter. sendMany only accept array of RequestDescriptor.');
            }

            $tempRequest= new Request();
            $tempRequest->setMethod($request->getMethod())
                    ->setUri($request->getUrl())
                    ->setAllHeaders($request->getHeaders())
                    ->setBody($this->handleBody($request->getBodyParams()))
            ;

            $requests[] = $tempRequest;
        }


        // No need to try catch here, Client::request() method itself will never throw
        $promises =  $this->getClient()->requestMulti($requests);

        $this->promises = array_merge($this->promises, $promises);

        return $promises;
    }

    /**
     * @param RequestDescriptor $requestDescriptor
     *
     * @param int               $flags
     *
     * @return Promise
     * @throws ApiClientException
     */
    public function send(RequestDescriptor $requestDescriptor, $flags = 1)
    {
        $request = new Request();
        $request->setMethod($requestDescriptor->getMethod())
            ->setUri($requestDescriptor->getUrl())
            ->setAllHeaders($requestDescriptor->getHeaders())
            ->setBody($this->handleBody($requestDescriptor->getBodyParams()))
        ;

        $promise = $this->getClient()->request($request);

        $responseDescriptor = new ResponseDescriptor();

        if($flags & ApiRequestOption::NO_RESPONSE)
        {
            $this->promises[] = $promise;
            return null;
        }

        /** @var Response $response */
        try{
            $response = \Amp\wait($promise);
        }catch (\Exception $e){
            die($request->getBody());
        }

        $responseDescriptor->setBody($response->getBody());
        $responseDescriptor->setCode($response->getStatus());
        $responseDescriptor->setHeaders($response->getAllHeaders());

        return $responseDescriptor;
    }

    protected function handleBody($body)
    {
        if(is_array($body))
        {
            $handledBody = new FormBody();
            $handledBody->addFields($body);
        }else{
            $handledBody = $body;
        }
        return $handledBody;

    }

    function __destruct()
    {
       \Amp\wait(\Amp\all($this->promises));
    }


}
