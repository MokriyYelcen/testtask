<?php


namespace App\ChatServices\WebSocketServices;
use App\{Http\Controllers\WebSocketController, User, Message, color};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MessageService
{
    protected $WebSocketController ;
    public function __construct(WebSocketController $WebSocketController)
    {
        $this->WebSocketController = $WebSocketController;
    }

    public function send($conn, $data){
        $conn->send(json_encode($data));
    }

    public function sendToAll($data){
        foreach ($this->WebSocketController->connections as $peer) {
            $peer->send(json_encode($data));
        }
    }
    public function sendToAdmins($data){
        foreach($this->WebSocketController->connections as $id => $peer){
            if($peer->user->isAdmin){
                $peer->send(json_encode($data));
            }

        }
    }
    public function saveMessage($authorId,$content){
        $message= new Message;
        $message->user_id=$authorId;
        $message->content=$content;
        $message->save();
    }

    public function validateMessage($user, $message)
    {
        if($user->muted ) return 'Your account was muted by admin, wait please';
        if (strlen($message->content) >= 255) return 'Invalid content, message have not been sent';
        if (self::ableToSend($user->id)) {
            return null;
        } else {
            return 'You can send messages no more than every 15 seconds ';
        }
    }





    public function getLastMessages(){
        $all= Message::with('user')->get();
        $res=[];
        foreach($all as $message){
            $res[]=[
                'type' => 'message',
                'status' => 'output',
                'sent'=>$message->created_at,
                'author'=>$message->user->login,
                'content'=>$message->content
            ];
        }
        return $res;
    }


    private static function ableToSend($id){
        $message=User::find($id)->Messages()->orderByDesc('created_at')->first();
        if($message){
            return ($message->created_at->diffInSeconds(Carbon::now())>15);
        }


        return true;
    }

    public function getUserMessageColor($id){
        $colorId=User::find($id)->color_id;

        return color::find($colorId)->name;
    }
}
