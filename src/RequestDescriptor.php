<?php

namespace Fei\ApiClient;

use Psr\Http\Message\StreamInterface;

/**
 * Class RequestDescriptor
 *
 * @package Fei\ApiClient
 */
class RequestDescriptor
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $params = array();

    /**
     * @var array
     */
    protected $bodyParams = array();

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var string|StreamInterface|resource|null
     */
    protected $rawData;

    /**
     * RequestDescriptor constructor.
     *
     * @param null $data
     */
    public function __construct($data = null)
    {
        if (!is_null($data)) {
            $this->hydrate($data);
        }
    }

    /**
     * @param $data
     *
     * @return $this
     * @throws ApiClientException
     */
    public function hydrate($data)
    {
        if ($data instanceof \ArrayObject) {
            $data = $data->getArrayCopy();
        }

        if ($data instanceof \Iterator) {
            $data = iterator_to_array($data);
        }

        if (!is_array($data)) {
            throw new ApiClientException('RequestDescriptor need an array, ArrayObject or Iterator instance to get hydrated');
        }

        foreach ($data as $property => $value) {
            $setter = 'set' . ucfirst($property);

            $this->$setter($value);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getBodyParams()
    {
        return $this->bodyParams;
    }

    /**
     * @param $bodyParams
     *
     * @return $this
     */
    public function setBodyParams($bodyParams)
    {
        $this->bodyParams = $bodyParams;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }


    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function addParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getParam($key)
    {
        return $this->params[$key];
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function addBodyParam($key, $value)
    {
        $this->bodyParams[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getBodyParam($key)
    {
        return $this->bodyParams[$key];
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getHeader($key)
    {
        return $this->headers[$key];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * Get RawData
     *
     * @return null|StreamInterface|resource|string
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * Set RawData
     *
     * @param null|StreamInterface|resource|string $rawData
     *
     * @return $this
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;

        return $this;
    }

    /**
     * @return null|StreamInterface|resource|string
     */
    public function fetchBody()
    {
        $rawParam = $this->getRawData();
        $bodyParams= $this->getBodyParams();

        if (!empty($rawParam) && !empty($bodyParams)) {
            throw new \LogicException('The body param and raw data can not be both filled');
        }

        if (!empty($bodyParams)) {
            return http_build_query($bodyParams);
        }

        if (!empty($rawParam)) {
            return $rawParam;
        }

        return null;
    }
}
