<?php


namespace App\ChatServices\MessageHandlers;


use App\ChatInterfaces\MessageHandler;
use App\ChatServices\WebSocketServices\MessageService;
use App\ChatServices\WebSocketServices\UserService;
use App\Http\Controllers\WebSocketController;
use Illuminate\Support\Facades\Log;
use Ratchet\ConnectionInterface;

class ChangeBannedHandler implements MessageHandler
{

    public function __construct(UserService $userService,messageService $messageService){
        $this->userService = $userService;
        $this->messageService = $messageService;
    }
    protected $userService;
    protected $messageService;

    public function handle(ConnectionInterface $conn,$message,WebSocketController $obj){
        if (!$conn->user->isAdmin) {
            return;
        }

        $target = $this->userService->getUserById($message->user);

//                if ($conn->isAdmin) {
        if($conn->user->id != $target->id){
            if(!$pastBannedStatus=$target->banned){
                if (array_key_exists($message->user,$this->connections)) {
                    $this->connections[$message->user]->close();
                    unset($this->connections[$message->user]);
                }else{
                    Log::debug('pastBannedStatus : '. $pastBannedStatus.'+'.'User i snot connected now');
                }
            }

            $this->UserService->changeBanned($message->user);
        }else{
            $this->send($conn,[
                'type' => 'message',
                'sent' => date("Y-m-d H:i:s"),
                'author' => 'System',
                'content' => 'suicide is not an option'

            ]);
        }

//                }

        $this->toAdmins([
            'type' => 'updateUserList',
            'userList' => $this->UserService->getAllUsersArray()
        ]);

//                foreach($this->connections as $id=> $peer){
//                    if($peer->isAdmin){
//                            Log::debug('Admin peer ==='.$peer->isAdmin);
//                            $peer->send(json_encode([
//                                'type' => 'updateUserList',
//                                'userList' => $this->UserService->getAllUsersArray()
//                            ]));
//                        Log::debug('sent to==='.$id);
//                        }
//
//                }

    }
}
