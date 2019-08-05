<?php

function publish($routingkey, $message)
{
    $segments = explode(':', $routingkey);

    if(count($segments) < 2){
        $queue = env('MESSAGE_QUEUE_NAME');
    }else{
        $queue = $segments[0]; 
        unset($segments[0]);
        $routingkey = implode('.', $segments);
    }

    return Amqp::publish($routingkey, is_string($message) ? $message : json_encode($message),[
        'queue' => $queue
    ]);
}