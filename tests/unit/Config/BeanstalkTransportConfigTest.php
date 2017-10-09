<?php

namespace Test\Fei\ApiClient\Transport;

use Fei\ApiClient\Config\BeanstalkTransportConfig;

class BeanstalkTransportConfigTest extends \Codeception\Test\Unit
{
    public function testBeanstalkServiceIdAccessor()
    {
        $config = new BeanstalkTransportConfig('service-id');
        $config->setBeanstalkServiceId('fake-id');

        $this->assertEquals($config->getBeanstalkServiceId(), 'fake-id');
        $this->assertAttributeEquals($config->getBeanstalkServiceId(), 'beanstalkServiceId', $config);
    }
}
