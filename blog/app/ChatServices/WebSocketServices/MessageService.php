<?php


namespace App\ChatServices\WebSocketServices;
use App\Message;
use App\User;

class MessageService
{
    public function saveMessage($authorId,$content){
        $message= new Message;
        $message->user_id=$authorId;
        $message->content=$content;
        $message->save();
    }
    public function validateMessage($msg){
        if(strlen($msg->content)<=200){
            return true;
        }
        return false;

    }


}