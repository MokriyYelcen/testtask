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

    public function __construct(UserService $userService,MessageService $messageService){
        $this->userService = $userService;
        $this->messageService = $messageService;
    }
    protected $userService;
    protected $messageService;

    public function handle(ConnectionInterface $conn,$message,WebSocketController $obj){
        if (!$conn->user->isAdmin) return;
        $target = $this->userService->getUserById($message->user);
        if($conn->user->id == $target->id){
            $this->messageService->send($conn,[
                'type' => 'message',
                'sent' => date("Y-m-d H:i:s"),
                'author' => 'System',
                'content' => 'suicide is not an option'

            ]);
            return;
        }
        if(!$pastBannedStatus=$target->banned){
            if (array_key_exists($message->user,$obj->connections)) {
                $obj->connections[$message->user]->close();
                unset($obj->connections[$message->user]);
            }
        }
        $this->userService->changeBanned($message->user);
        $this->messageService->sendToAdmins([
            'type' => 'updateUserList',
            'userList' => $this->userService->getAllUsersArray()
        ]);
    }
}
