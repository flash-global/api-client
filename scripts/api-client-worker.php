#!env php
<?php
    
    use Fei\ApiClient\Transport\BasicTransport;
    use Fei\ApiClient\Worker\BeanstalkProxyWorker;
    use Pheanstalk\Pheanstalk;
    
    require __DIR__ . '/../vendor/autoload.php';
    
    
    // handle options
    
    $shortOptions = 'h:p:d:v';
    $longOptions = array('verbose', 'delay', 'host', 'port');
    
    $options = getopt($shortOptions, $longOptions);
    
    $host = isset($options['host']) ? $options['host'] : isset($options['h']) ? $options['h'] : 'localhost';
    $port = isset($options['port']) ? (int) $options['port'] : isset($options['p']) ? (int) $options['p'] : 11300;
    $verbose = (isset($options['v']) ||isset($options['verbose']));
    $delay = (isset($options['delay'])) ? (int) $options['delay'] : (isset($options['d'])) ? (int) $options['d'] : 3;
    
    $mode = ($verbose) ? BeanstalkProxyWorker::VERBOSE : 0;
    
    $pheanstalk = new Pheanstalk($host, $port);
    $transport = new BasicTransport();
    $worker = new BeanstalkProxyWorker();
    $worker->setPheanstalk($pheanstalk)->setTransport($transport);
    
    if($mode & BeanstalkProxyWorker::VERBOSE)
    {
        printf('Working on queue hosted on %s:%s, with delay between polls being %s seconds', $host, $port, $delay);
        echo PHP_EOL;
    }
while(true)
{
    try
    {
        $return = $worker->run($mode);
        
        if($return > 0)
        {
            sleep($delay);
        }
    } catch(\Exception $e)
    {
        if($mode & BeanstalkProxyWorker::VERBOSE)
        {
            echo "\t [ ERROR ]" . $e->getMessage() . PHP_EOL;
        }
        exit($e->getCode());
    }
    
}
    
    
