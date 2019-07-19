<?php


namespace App\ChatServices\WebSocketServices;
use App\{User,Message,color};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MessageService
{
    public function saveMessage($authorId,$content){
        $message= new Message;
        $message->user_id=$authorId;
        $message->content=$content;
        $message->save();
    }

    public function validateMessage($user, $message)
    {
        if (!$user->muted) {
            Log::debug('not muted');
            if (strlen($message->content) <= 255) {
                Log::debug('content less 255');
                Log::debug('calling ableToSend');
                if (self::ableToSend($user->id)) {
                    Log::debug('ableToSend ok');
                    return null;
                } else {
                    Log::debug('ableToSend fail');
                    return 'You can send messages no more than every 15 seconds ';
                }
            } else {
                Log::debug('invalid content');
                return 'Invalid content, message have not been sent';
            }
        } else {
            Log::debug('account muted');
            return 'Your account was muted by admin, wait please';
        }
        Log::debug('validated');
        return null;
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


    private static function ableToSend($id){
        Log::debug('ableToSend start');
        $message=User::find($id)->Messages()->orderByDesc('created_at')->first();
        Log::debug($message);
        if($message){
            Log::debug($message->created_at->diffInSeconds(Carbon::now())>15);
            return ($message->created_at->diffInSeconds(Carbon::now())>15);
        }


        Log::debug('return true from ableToSend ');
        return true;
    }

    public function getUserMessageColor($id){
        $colorId=User::find($id)->color_id;

        return color::find($colorId)->name;
    }
}