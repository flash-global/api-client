<?php

namespace Test\Fei\ApiClient\Transport;

use Fei\ApiClient\Config\AbstractClientConfig;
use Fei\ApiClient\Config\BasicTransportConfig;
use Fei\ApiClient\Config\BeanstalkTransportConfig;

class AbstractClientConfigTest extends \Codeception\Test\Unit
{
    public function testServiceIdentifierAccessor()
    {
        /** @var AbstractClientConfig $config */
        $config = $this->getClientConfigClass();
        $config->setServiceIdentifier('fake-id');

        $this->assertEquals($config->getServiceIdentifier(), 'fake-id');
        $this->assertAttributeEquals($config->getServiceIdentifier(), 'serviceIdentifier', $config);
    }

    public function testServiceBaseUrlAccessor()
    {
        /** @var AbstractClientConfig $config */
        $config = $this->getClientConfigClass();
        $config->setServiceBaseUrl('fake-url');

        $this->assertEquals($config->getServiceBaseUrl(), 'fake-url');
        $this->assertAttributeEquals($config->getServiceBaseUrl(), 'serviceBaseUrl', $config);
    }

    public function testParamsAccessor()
    {
        /** @var AbstractClientConfig $config */
        $config = $this->getClientConfigClass();
        $config->setParams(['fake-param']);

        $this->assertEquals($config->getParams(), ['fake-param']);
        $this->assertAttributeEquals($config->getParams(), 'params', $config);
    }

    public function testSetSyncTransportConfig()
    {
        /** @var AbstractClientConfig $config */
        $config = $this->getClientConfigClass();

        $basicTransportMock = $this->getMockBuilder(BasicTransportConfig::class)->getMock();
        $config->setSyncTransportConfig($basicTransportMock);

        $this->assertEquals([
            'sync' => $basicTransportMock
        ], $config->getTransportConfig());
    }

    public function testSetAsyncTransportConfig()
    {
        /** @var AbstractClientConfig $config */
        $config = $this->getClientConfigClass();

        $asyncTransportMock = $this->getMockBuilder(BeanstalkTransportConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config->setAsyncTransportConfig($asyncTransportMock);

        $this->assertEquals([
            'async' => $asyncTransportMock
        ], $config->getTransportConfig());
    }

    protected function getClientConfigClass()
    {
        return new class('http://base-url.com') extends AbstractClientConfig {
        };
    }
}
