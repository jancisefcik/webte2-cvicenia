<?php
use Workerman\Worker;
use Workerman\Connection\TcpConnection;

require_once __DIR__ . '/vendor/autoload.php';

$ws_worker = new Worker("websocket://0.0.0.0:8282");
$ws_worker->count = 1; // 1 proces


$ws_worker->onConnect = function($connection) {
    $uuid = uniqid();
    $connection->send(json_encode(["uuid" => $uuid]));  
};

// When receiving data from the client, return "hello $data" to the client
$ws_worker->onMessage = function(TcpConnection $connection, $data) use ($ws_worker)
{
    foreach ($ws_worker->connections as $conn) {
        $conn->send($data);
    }
};

// Run the worker
Worker::runAll();