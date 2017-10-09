<?php
namespace Fei\ApiClient\Config;

/**
 * Class AbstractTransportConfig
 *
 * @package Fei\ApiClient\Config
 */
abstract class AbstractTransportConfig
{
    /**
     * @var array Represents the options passed to the HTTP Transport
     */
    protected $options;

    /**
     * TransportConfig constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Get the options of the transport
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set the options of the transport
     *
     * @param array $options
     *
     * @return AbstractTransportConfig
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }
}
