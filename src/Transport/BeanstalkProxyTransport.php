<?php

namespace Fei\ApiClient\Transport;

use Fei\ApiClient\Constants;
use Fei\ApiClient\RequestDescriptor;
use Pheanstalk\Pheanstalk;

/**
 * Class BeanstalkProxyTransport
 *
 * @package Fei\ApiClient\Transport
 */
class BeanstalkProxyTransport extends AbstractTransport implements AsyncTransportInterface
{
    const OPTION_PHEANSTALK = 'pheanstalk';
    const OPTION_TUBE       = 'tube';

    /**
     * @var Pheanstalk
     */
    protected $pheanstalk;

    /**
     * @var string
     */
    protected $tube = Constants::DEFAULT_BEANSTALK_TUBE;

    /**
     * @param RequestDescriptor $requestDescriptor
     * @param int $flags
     * @return $this|\Fei\ApiClient\ResponseDescriptor
     * @throws TransportException
     */
    public function send(RequestDescriptor $requestDescriptor, $flags = 0)
    {
        $pheanstalk = $this->getPheanstalk();
        $connection = $pheanstalk->getConnection();

        if (!$connection->isServiceListening()) {
            throw new TransportException(sprintf('No beanstalk server is currently listening on %s:%s', $connection->getHost(), $connection->getPort()));
        }

        $this->getPheanstalk()->putInTube($this->getTube(), json_encode($requestDescriptor->toArray()));

        return $this;
    }

    /**
     * @param array $requests
     * @return $this|mixed
     * @throws TransportException
     */
    public function sendMany(array $requests)
    {
        foreach ($requests as $request) {
            $this->send($request);
        }

        return $this;
    }


    /**
     * @return string
     */
    public function getTube()
    {
        return $this->tube;
    }

    /**
     * @param string $tube
     *
     * @return $this
     */
    public function setTube($tube)
    {
        $this->tube = $tube;

        return $this;
    }

    /**
     * @return Pheanstalk
     */
    public function getPheanstalk()
    {
        return $this->pheanstalk;
    }

    /**
     * @param Pheanstalk $pheanstalk
     *
     * @return $this
     */
    public function setPheanstalk($pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;

        return $this;
    }
}
