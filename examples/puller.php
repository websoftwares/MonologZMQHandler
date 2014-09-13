<?php
try {
    $context = new \ZMQContext();
    $subscriber = new \ZMQSocket($context, \ZMQ::SOCKET_PULL);
    $subscriber->connect("tcp://127.0.0.1:5556");
    while (true) {
        $msg = $subscriber->recv();
        var_dump(json_decode($msg));
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}