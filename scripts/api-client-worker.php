#!/usr/bin/env php
<?php

use Fei\ApiClient\Constants;
use Fei\ApiClient\Transport\BasicTransport;
use Fei\ApiClient\Worker\BeanstalkProxyWorker;
use Pheanstalk\Pheanstalk;

$autoloadFiles = array(
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php'
);

foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require_once $autoloadFile;
    }
}

// handle options

$shortOptions = 'h:p:d:v:t';
$longOptions = ['verbose', 'delay', 'host', 'port', 'tube'];

$options = getopt($shortOptions, $longOptions);

$host = $options['host'] ?? $options['h'] ?? 'localhost';
$port = (int)($options['port'] ?? $options['p'] ?? 11300);
$verbose = (isset($options['v']) || isset($options['verbose']));
$delay = (int)($options['delay'] ?? $options['d'] ?? 3);
$tube = $options['tube'] ?? $options['t'] ?? Constants::DEFAULT_BEANSTALK_TUBE;

$mode = ($verbose) ? BeanstalkProxyWorker::VERBOSE : 0;

$pheanstalk = new Pheanstalk($host, $port);
$transport = new BasicTransport();
$worker = (new BeanstalkProxyWorker())
    ->setPheanstalk($pheanstalk)
    ->setTransport($transport)
    ->setTube($tube);

if ($mode & BeanstalkProxyWorker::VERBOSE) {
    printf('Working on queue hosted on %s:%s, with delay between polls being %s seconds on tube %s', $host, $port, $delay, $tube);
    echo PHP_EOL;
}

while (true) {
    try {
        $return = $worker->run($mode);

        if ($return > 0) {
            sleep($delay);
        }
    } catch (\Exception $e) {
        if ($mode & BeanstalkProxyWorker::VERBOSE) {
            echo "\t [ ERROR ]" . $e->getMessage() . PHP_EOL;
        }

        exit($e->getCode());
    }
}
