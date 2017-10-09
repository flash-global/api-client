<?php
namespace Fei\ApiClient\Config;

use ObjectivePHP\Config\SingleDirective;

/**
 * Class AbstractClientConfig
 *
 * @package Fei\ApiClient\Config
 */
abstract class AbstractClientConfig extends SingleDirective
{
    /**
     * @var string identifier of the service (used to get it from the service container)
     */
    protected $serviceIdentifier;

    /**
     * @var AbstractTransportConfig[] array of transport configs
     */
    protected $transportConfig = [];

    /**
     * @var string url where the client can communicate with the service
     */
    protected $serviceBaseUrl;

    /**
     * @var array options set in the client class
     */
    protected $params = [];

    /**
     * AbstractClientConfig constructor.
     *
     * @param string $serviceBaseUrl url where the client can communicate with the service
     * @param array $params list of params set in the client class
     */
    public function __construct(string $serviceBaseUrl, array $params = [])
    {
        $this->setServiceBaseUrl($serviceBaseUrl);
        $this->setParams($params);
    }

    /**
     * Get ServiceIdentifier
     *
     * @return string
     */
    public function getServiceIdentifier(): string
    {
        return $this->serviceIdentifier;
    }

    /**
     * Set ServiceIdentifier
     *
     * @param string $serviceIdentifier the identifier of the service
     *
     * @return AbstractClientConfig
     */
    public function setServiceIdentifier(string $serviceIdentifier): self
    {
        $this->serviceIdentifier = $serviceIdentifier;

        return $this;
    }

    /**
     * Set sync transport config
     *
     * @param BasicTransportConfig $transportConfig
     *
     * @return AbstractClientConfig
     */
    public function setSyncTransportConfig(BasicTransportConfig $transportConfig): self
    {
        $this->transportConfig['sync'] = $transportConfig;

        return $this;
    }

    /**
     * Set async transport config
     *
     * @param BeanstalkTransportConfig $transportConfig
     *
     * @return AbstractClientConfig
     */
    public function setAsyncTransportConfig(BeanstalkTransportConfig $transportConfig): self
    {
        $this->transportConfig['async'] = $transportConfig;

        return $this;
    }

    /**
     * Get all the configurations for the transports
     *
     * @return array
     */
    public function getTransportConfig(): array
    {
        return $this->transportConfig;
    }

    /**
     * Get ServiceBaseUrl
     *
     * @return string
     */
    public function getServiceBaseUrl(): string
    {
        return $this->serviceBaseUrl;
    }

    /**
     * Set ServiceBaseUrl
     *
     * @param string $serviceBaseUrl
     *
     * @return AbstractClientConfig
     */
    public function setServiceBaseUrl($serviceBaseUrl): self
    {
        $this->serviceBaseUrl = $serviceBaseUrl;
        return $this;
    }


    /**
     * Get Params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set Params
     *
     * @param array $params
     *
     * @return AbstractClientConfig
     */
    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }
}
