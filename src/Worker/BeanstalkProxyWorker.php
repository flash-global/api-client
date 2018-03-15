<?php

namespace Fei\ApiClient\Worker;

use Fei\ApiClient\RequestDescriptor;
use Fei\ApiClient\Transport\SyncTransportInterface;
use Fei\ApiClient\Transport\TransportException;
use Fei\ApiClient\Transport\TransportInterface;
use Pheanstalk\Pheanstalk;

/**
 * Class BeanstalkProxyWorker
 *
 * @package Fei\ApiClient\Worker
 */
class BeanstalkProxyWorker
{

    const VERBOSE = 1;

    /**
     * @var Pheanstalk
     */
    protected $pheanstalk;

    /**
     * @var string
     */
    protected $tube = 'api-requests';

    /**
     * @var SyncTransportInterface
     */
    protected $transport;


    public function run($mode = 0)
    {
        $job = $this->getPheanstalk()->reserveFromTube($this->getTube());

        $request = new RequestDescriptor(json_decode($job->getData(), true));

        try {
            if ($mode && self::VERBOSE) {
                echo "\tRequesting API on " . $request->getUrl() . " using " . $request->getMethod() . " method". PHP_EOL;
            }
            $response = $this->getTransport()->send($request);

            if (!$response || !$response->isSuccess()) {
                // TODO log issues?
                throw new WorkerException(sprintf('Failed running job %d from %s (return code: %s)', $job->getId(), $this->getTube(), $response->getCode()));
            }

            $this->getPheanstalk()->delete($job);
        } catch (\Exception $e) {
            if ($mode && self::VERBOSE) {
                echo "\t [ ERROR ] " . $e->getMessage() . PHP_EOL;
            }

            if ($e instanceof TransportException) {
                // keep job in the active queue
            } else {
                // bury job until a solution is found
                $this->getPheanstalk()->bury($job);
            }
        }
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
    public function setPheanstalk(Pheanstalk $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;

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
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param SyncTransportInterface $transport
     *
     * @return $this
     */
    public function setTransport(SyncTransportInterface $transport)
    {
        $this->transport = $transport;

        return $this;
    }
}
