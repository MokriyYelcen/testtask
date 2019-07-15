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

    public function getLastMessages(){
        $all= Message::all();
        $res=[];
        foreach($all as $message){
            $res[]=[
                'type' => 'message',
                'status' => 'output',
                'sent'=>$message->created_at,
                'author'=>User::find($message->user_id)->login,
                'content'=>$message->content
            ];
        }
        return $res;
    }


}