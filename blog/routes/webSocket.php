<?php
Message::type('message',function(Ratchet\ConnectionInterface $conn,$mess,App\Http\Controllers\WebSocketController $obj){
//    dd($conn->user->login);
    $conn->send(json_encode([
        'type' => 'message',
        'sent' => date("Y-m-d H:i:s"),
        'author' => 'System',
        'content' => 'echo: '.$mess->content.' active: '.count($obj->connections).' users'
    ]));
});
