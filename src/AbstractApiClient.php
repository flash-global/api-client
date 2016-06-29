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
 * Class AbstractApiApiClient
 * @package Fei\ApiClient
 */
abstract class AbstractApiClient implements ApiClientInterface
{

    /**
     * @var string
     */
    protected $baseUrl;

    /** @var  TransportInterface */
    protected $transport;

    /**
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param TransportInterface $transport
     *
     * @return $this
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/4') . '/';

        return $this;
    }


}
