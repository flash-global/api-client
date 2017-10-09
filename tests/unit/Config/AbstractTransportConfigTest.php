<?php

namespace Test\Fei\ApiClient\Transport;

use Fei\ApiClient\Config\AbstractTransportConfig;

class AbstractTransportConfigTest extends \Codeception\Test\Unit
{
    public function testOptionAccessor()
    {
        /** @var AbstractTransportConfig $config */
        $config = new class extends AbstractTransportConfig {
        };
        $config->setOptions(['options']);

        $this->assertEquals($config->getOptions(), ['options']);
        $this->assertAttributeEquals($config->getOptions(), 'options', $config);
    }
}
