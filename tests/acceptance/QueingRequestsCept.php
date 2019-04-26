<?php
    
use Fei\ApiClient\RequestDescriptor;
use Fei\ApiClient\Transport\BeanstalkProxyTransport;
use Pheanstalk\Pheanstalk;
use \Tests\Fei\ApiClient\AcceptanceTester;

$I = new AcceptanceTester($scenario);
$I->wantTo('check queuing mails is working');
$scenario->skip();

$pheanstalk = new Pheanstalk('localhost');

if (!$pheanstalk->getConnection()->isServiceListening()) {
    $scenario->skip('No beanstalkd queue is listening on localhost:11300 to perform test');
}

$transport = new BeanstalkProxyTransport([BeanstalkProxyTransport::OPTION_PHEANSTALK => $pheanstalk]);

$I->clearQueue($transport->getTube());

$request = new RequestDescriptor();
$request->setUrl('http://localhost:8080/api/notifications');
$request->setMethod('POST');
$request->addBodyParam('notification', '{"message": "notification message", "namespace": "/postman", "origin": "http","context":[{"key":"test","value":"test value"}]}');


$transport->send($request);

$I->seeQueueHasCurrentCount($transport->getTube(), 1);
