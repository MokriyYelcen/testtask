<?php
namespace App\ChatInterfaces;
use App\Http\Controllers\WebSocketController;
use Ratchet\ConnectionInterface;

interface MessageHandler{
    public function handle(ConnectionInterface $conn,$message,WebSocketController $obj);
}
