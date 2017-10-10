<?php

namespace Fei\ApiClient\Transport;

use Fei\ApiClient\ApiClientException;

/**
 * Class AbstractTransport
 *
 * @package Fei\ApiClient\Transport
 */
abstract class AbstractTransport implements TransportInterface
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var array
     */
    protected $availableOptions = array();

    /**
     * AbstractApiClient constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->initOptions();
        $this->setOptions($options);
    }

    /**
     * Initialize availableOptions property based on existing OPTION_* constants
     */
    protected function initOptions()
    {
        $reflectedClient = new \ReflectionObject($this);
        $constants       = $reflectedClient->getConstants();

        foreach ($constants as $constant => $value)
        {
            if (strpos($constant, 'OPTION_') === 0)
            {
                $this->availableOptions[] = $value;
            }
        }
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value)
        {
            $this->setOption($option, $value);
        }

        return $this;
    }

    /**
     * @param $option
     * @param $value
     *
     * @return $this
     *
     * @throws ApiClientException
     */
    public function setOption($option, $value)
    {
        if (in_array($option, $this->availableOptions))
        {
            $this->$option = $value;

            return $this;
        }

        throw new ApiClientException(sprintf('Trying to set unknown option "%s" on %s ', $option, get_class()));

    }
}
