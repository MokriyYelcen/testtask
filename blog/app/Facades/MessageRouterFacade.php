<?php
namespace App\Facades ;
use Illuminate\Support\Facades\Facade;
use App\ChatServices\WebSocketServices\MessageRouter;


class MessageRouterFacade extends Facade{
    protected static function getFacadeAccessor()
    {
        return MessageRouter::class;
    }
}
