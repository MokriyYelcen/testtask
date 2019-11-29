<?php


namespace App\ChatServices\MessageHandlers;

use App\ChatInterfaces\MessageHandler;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\WebSocketController;
use Ratchet\ConnectionInterface;
use App\ChatServices\WebSocketServices\{UserService, messageService};

class GlobalMessageHandler implements MessageHandler
{
    public function __construct(UserService $userService,messageService $messageService){
        $this->userService = $userService;
        $this->messageService = $messageService;
    }
    protected $userService;
    protected $messageService;

    public function handle(ConnectionInterface $conn,$message,WebSocketController $obj){

        if ($fail=$this->messageService->validateMessage($conn->user,$message)){

            $this->messageService->send($conn, [
                'type' => 'message',
                'sent' => date("Y-m-d H:i:s"),
                'author' => 'System',
                'content' => $fail
            ]);

            return;
        }

        $this->messageService->saveMessage($conn->user->id, $message->content);

        $this->messageService->sendToAll([
            'type' => 'message',
            'sent' => date("Y-m-d H:i:s"),
            'author' => $conn->user->login,
            'content' => $message->content,
            'color'=>$this->messageService->getUserMessageColor($conn->user->id)
        ]);
    }


}
