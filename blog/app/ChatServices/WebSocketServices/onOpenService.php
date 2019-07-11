<?php


namespace App\ChatServices\WebSocketServices;

use App\User;


class onOpenService
{
    function sayHello(){
        return json_encode(['hello'=>'world']);
    }

    function checkUserToken($token){


    }
    function checkUserRole($token){


    }

}