<?php
namespace Fei\ApiClient\Config;

/**
 * Class used to define the config of the async transport with beanstalk
 *
 * @package Fei\ApiClient\Config
 */
class BeanstalkTransportConfig extends AbstractTransportConfig
{
    /**
     * Identifier of the beanstalk service
     *
     * @var string
     */
    protected $beanstalkServiceId;

    /**
     * BeanstalkTransportConfig constructor.
     *
     * @param string $beanstalkServiceId Identifier of the beanstalk service
     * @param array $options Options for the beanstalk proxy transport
     */
    public function __construct(string $beanstalkServiceId, array $options = [])
    {
        parent::__construct($options);

        $this->setBeanstalkServiceId($beanstalkServiceId);
    }

    /**
     * Get the identifier of the beanstalk service
     *
     * @return string
     */
    public function getBeanstalkServiceId(): string
    {
        return $this->beanstalkServiceId;
    }

    /**
     * Set the identifier of the beanstalk service
     *
     * @param string $beanstalkServiceId identifier of the beanstalk service
     *
     * @return self
     */
    public function setBeanstalkServiceId(string $beanstalkServiceId): self
    {
        $this->beanstalkServiceId = $beanstalkServiceId;

        return $this;
    }
}
