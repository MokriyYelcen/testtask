<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Facades\MessageRouterFacade as Message;
use App\ChatServices\WebSocketServices\{UserService, MessageService};

class WebSocketController extends Controller implements MessageComponentInterface
{
    public $connections = [];

    public $UserService;
    public $MessageService;
    protected $router;


    public function __construct()
    {
        $this->UserService = new UserService;
        $this->MessageService = new MessageService($this);
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
          $conn->user = $user;

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
        Log::debug((new \DateTime())->format('Y-m-d H:i:s').') user: '.$conn->name.
            ', get the next error: {'.PHP_EOL.$e.PHP_EOL.'}');
    }

    /**
     * Triggered when a client sends data through the socket
     * @param \Ratchet\ConnectionInterface $conn The socket/connection that sent the message to your application
     * @param string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $conn, $msg)
    {
        try{
            Message::message($conn,$msg);
        }catch(\Exception $e){
            $conn->send(json_encode( [
                'type' => 'message',
                'sent' => date("Y-m-d H:i:s"),
                'author' => 'System',
                'content' => (string)$e
            ]));
        }
    }


}

