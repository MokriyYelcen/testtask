<?php


namespace App\ChatServices\MessageHandlers;


use App\ChatInterfaces\MessageHandler;
use App\ChatServices\WebSocketServices\MessageService;
use App\ChatServices\WebSocketServices\UserService;
use App\Http\Controllers\WebSocketController;
use App\User;
use Illuminate\Support\Facades\Log;
use Ratchet\ConnectionInterface;

class GetUserListHandler implements MessageHandler
{

    public function __construct(UserService $userService,MessageService $messageService){
        $this->userService = $userService;
        $this->messageService = $messageService;
    }
    protected $userService;
    protected $messageService;

    public function handle(ConnectionInterface $conn,$message,WebSocketController $obj){
        if ($conn->user->isAdmin) {
            $this->messageService->send($conn,[
                'type' => 'updateUserList',
                'userList' => $this->userService->getAllUsersArray()
            ]);
        }
    }
}
