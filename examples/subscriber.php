<?php
try {
    $context = new \ZMQContext();
    $subscriber = new \ZMQSocket($context, \ZMQ::SOCKET_SUB);
    $subscriber->connect("tcp://127.0.0.1:5556");
    $subscriber->setSockOpt(\ZMQ::SOCKOPT_SUBSCRIBE, 'log-channel');

    while (true) {
        $msg = $subscriber->recv();
        if ($msg == 'log-channel') {
            printf("printing logs for channel %s \n", $msg);
        } else {
            var_dump(json_decode($msg));
        }
    }

} catch (\Exception $e) {
    echo $e->getMessage();
}