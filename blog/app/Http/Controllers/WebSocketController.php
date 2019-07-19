<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\ChatServices\WebSocketServices\{UserService, MessageService};


class WebSocketController extends Controller implements MessageComponentInterface
{
    private $connections = [];

    public $UserService;
    public $MessageService;


    public function __construct()
    {
        $this->UserService = new UserService;
        $this->MessageService = new MessageService;

    }


    /**
     * When a new connection is opened it will be passed to this method
     * @param ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        $user = $this->UserService->getUserByConnection($conn);

        if ($user && !$user->banned) {
//            $conn->user = $user;

            $conn->username = $user->login;
            if ($this->UserService->isAdmin($user->id)) {
                $conn->isAdmin = true;
                $conn->send(json_encode([
                    'type' => 'admin',
                ]));
            }


            $this->connections[$user->id] = $conn;
            $onlineList = [];

            foreach ($this->connections as $peer) {
                $onlineList[] = $peer->username;
            }

            $conn->send(json_encode([
                'type' => 'oldMessages',
                'messages' => $this->MessageService->getLastMessages()
            ]));

            foreach ($this->connections as $peer) {
                $peer->send(json_encode([
                    'type' => 'updateOnlineList',
                    'onlineList' => $onlineList
                ]));
            }
        } else {
            $conn->close();
        }

    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        $user = $this->UserService->getUserByConnection($conn);
        unset($this->connections[$user->id]);

        $onlineList = [];
        foreach ($this->connections as $peer) {
            $onlineList[] = $peer->username;

        }

        foreach ($this->connections as $peer) {
            $peer->send(json_encode([
                'type' => 'updateOnlineList',
                'onlineList' => $onlineList
            ]));
        }

    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        /*
        $userId = $this->connections[$conn->resourceId]['user_id'];
        echo "An error has occurred with user $userId: {$e->getMessage()}\n";
        unset($this->connections[$conn->resourceId]);
        $conn->close();*/
    }

    /**
     * Triggered when a client sends data through the socket
     * @param \Ratchet\ConnectionInterface $conn The socket/connection that sent the message to your application
     * @param string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $conn, $msg)
    {
        $user = $this->UserService->getUserByConnection($conn);
        $message = json_decode($msg);

        switch ($message->type) {
            case'message':
                Log::debug($msg);

                if (!$fail=$this->MessageService->validateMessage($user,$message)){
                    Log::debug($fail);

                    $this->send($conn, [
                        'type' => 'message',
                        'sent' => date("Y-m-d H:i:s"),
                        'author' => 'System',
                        'content' => $fail
                    ]);

                    return;
                }
                
                $this->MessageService->saveMessage($user->id, $message->content);

                $this->sendToAll([
                    'type' => 'message',
                    'sent' => date("Y-m-d H:i:s"),
                    'author' => $user->login,
                    'content' => $message->content,
                    'color'=>$this->MessageService->getUserMessageColor($user->id)
                ]);

                break;

            case 'changeMuted':
                Log::debug('changeMuted');

                if (!$conn->isAdmin) {
                    return;
                }

                Log::debug('isAdmin');
                $this->UserService->changeMuted($message->user);

                $this->toAdmins([
                    'type' => 'updateUserList',
                    'userList' => $this->UserService->getAllUsersArray()
                ]);

                break;

            case 'changeBanned':
                if (!$conn->isAdmin) {
                    return;
                }

                $target = $this->UserService->getUserById($message->user);

//                if ($conn->isAdmin) {
                if($user->id != $target->id){
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

                break;

            case'getUserList':
                if ($conn->isAdmin) {
                    $conn->send(json_encode([
                        'type' => 'updateUserList',
                        'userList' => $this->UserService->getAllUsersArray()
                    ]));
                }
                break;


        }


    }

    public function toAdmins($data){
        foreach($this->connections as $id=> $peer){
            if($peer->isAdmin){
                Log::debug('Admin peer ==='.$peer->isAdmin);
                $peer->send(json_encode($data));
                Log::debug('sent to==='.$id);
            }

        }
    }

    public function send($conn, $data){
        $conn->send(json_encode($data));
    }

    public function sendToAll($data){
        foreach ($this->connections as $peer) {
            $peer->send(json_encode($data));
        }
    }
}
/*
 * Log::debug('changeBanned');
                Log::debug('Before the changeBanned methos array connections '.count($this->connections));
                foreach ($this->connections as $id =>$peer) {
                    Log::debug($id.'==='.$peer);
//                        if($peer->isAdmin){
//                            Log::debug('Admin peer ==='.$peer->isAdmin);
//                            $peer->send(json_encode([
//                                'type' => 'updateUserList',
//                                'userList' => $this->UserService->getAllUsersArray()
//                            ]));
//                        }

                }
                if ($conn->isAdmin) {
                    Log::debug('isAdmin');
                    $pastBannedStatus=$this->UserService->getUserById($message->user)->banned;
                    Log::debug('$pastBannedStatus' . $pastBannedStatus);
                    Log::debug('Before the changeBanned methos array connections '.count($this->connections).'elements the first'.$this->connections[1]);
                    $this->UserService->changeBanned($message->user);
                    Log::debug('changeBanned fired');
                    Log::debug('connections '.count($this->connections));
                    Log::debug($this->connections[1]);
                    foreach ($this->connections as $id =>$peer) {
                        Log::debug($id.'==='.$peer);
//                        if($peer->isAdmin){
//                            Log::debug('Admin peer ==='.$peer->isAdmin);
//                            $peer->send(json_encode([
//                                'type' => 'updateUserList',
//                                'userList' => $this->UserService->getAllUsersArray()
//                            ]));
//                        }

                    }
//                    if(!$pastBannedStatus){
//                        if ($this->connections[$message->user]) {
//                            $this->connections[$message->user]->close();
//                            unset($this->connections[$message->user]);
//                        }
//                    }
                }*/
