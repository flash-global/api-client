# Api Client
[![GitHub license](https://img.shields.io/github/license/flash-global/api-client.svg)](https://github.com/flash-global/api-client)![continuousphp](https://img.shields.io/continuousphp/git-hub/flash-global/api-client.svg)[![GitHub issues](https://img.shields.io/github/issues/flash-global/api-client.svg)](https://github.com/flash-global/api-client/issues)


Low level API client.

All API client should use this library and extend `\Fei\ApiClient\AbstractApiClient` abstract class.

## Installation

Use Composer: `composer.phar require fei/mailer-client`

Or add this requirement `"fei/api-client": "^1.1.0"` to your `composer.json` file.

## Use the `BeanstalkProxyWorker`

The `BeanstalkProxyWorker` is used for consume Beanstalkd messages queue and send them to the targeted api server.

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Fei\ApiClient\Transport\BasicTransport;
use Fei\ApiClient\Worker\BeanstalkProxyWorker;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;

$host = "127.0.0.1";
$port = PheanstalkInterface::DEFAULT_PORT;

$worker = new BeanstalkProxyWorker();
$worker->setPheanstalk(new Pheanstalk($host, $port));
$worker->setTransport(new BasicTransport());

$worker->run(BeanstalkProxyWorker::VERBOSE);
```

This example will consume one message from Beanstakld tube (or queue) and send it to the api server.
 
For handle more messages you should create a infinite loop !

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Fei\ApiClient\Transport\BasicTransport;
use Fei\ApiClient\Worker\BeanstalkProxyWorker;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;

$host = "127.0.0.1";
$port = PheanstalkInterface::DEFAULT_PORT;

$worker = new BeanstalkProxyWorker();
$worker->setPheanstalk(new Pheanstalk($host, $port));
$worker->setTransport(new BasicTransport());

while (true) {
    $worker->run(BeanstalkProxyWorker::VERBOSE);
}
```

For a reliable worker, please use `script/api-client-worker.php`:

```
api-client-worker.php -h 127.0.0.1 -p 11300 -d 5
```
