<?php


namespace App\ChatServices\WebSocketServices;

use App\{User,Message};


class UserService
{

    public function __construct(){

    }
    function sayHello(){
        return json_encode(['hello'=>' Im` message from server']);
    }

    function getUserByConnection($conn){
        $token = $conn->httpRequest->getUri()->getQuery();
        $Users=User::where('token',$token);
        if($Users->count()==1){
            return $Users->first();
        }
        return false;


    }

    function isAdmin($token){
        $probablyAdmin=User::where('token',$token)->first();
        if($probablyAdmin->isAdmin===1){
            return true;
        }
        return false;

    }
/*
    function isReady(User $user){
        $lastMessagetime=$user->Messages()->orderByDesc('created_at')->first()->createdAt;
        if(Message)

    }*/

}