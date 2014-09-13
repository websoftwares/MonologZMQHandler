<?php
include '../vendor/autoload.php';

use Websoftwares\Monolog\Handler\ZMQHandler;
use Monolog\Logger;

try {
    $context = new \ZMQContext();
    $publisher = new \ZMQSocket($context, \ZMQ::SOCKET_PUB);
    $publisher->bind("tcp://127.0.0.1:5556");

    $handler = new ZMQHandler($publisher, \ZMQ::MODE_SNDMORE); 

    $log = new Logger('log-channel');
    $log->pushHandler($handler);

    $i=0;

    print "Publising on port 5556";
    while(true){

            if ($i % 2 == 0) {

                // Log something
                $log->addWarning("Something is going wrong...");
            }
            else {
                // Log something
                $log->addNotice("Something is going fine :D");
            }

        $i++;

        sleep(1);
    }
} catch (\Exception $e) {
    echo $e->getMessage();

}