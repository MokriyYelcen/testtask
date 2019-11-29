<?php


namespace App\ChatServices\MessageHandlers;


use App\ChatInterfaces\MessageHandler;
use App\ChatServices\WebSocketServices\MessageService;
use App\ChatServices\WebSocketServices\UserService;
use App\Http\Controllers\WebSocketController;
use Illuminate\Support\Facades\Log;
use Ratchet\ConnectionInterface;

class ChangeMutedHandler implements MessageHandler
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

        $this->userService->changeMuted($message->user);

        $this->messageService->sendToAdmins([
            'type' => 'updateUserList',
            'userList' => $this->userService->getAllUsersArray()
        ]);
    }
}
