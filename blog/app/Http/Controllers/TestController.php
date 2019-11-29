<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Facades\MessageRouterFacade as Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\{ChatServices\WebSocketServices\MessageService,
    ChatServices\WebSocketServices\UserService,
    User,
    color,
    ChatServices\MessageHandlers\GlobalMessageHandler};

class TestController extends Controller
{
    public function __construct(UserService $userService,messageService $messageService){
        $this->userService = $userService;
        $this->messageService = $messageService;
    }
    protected $userService;
    protected $messageService;
    public function test(Request $request){
//        $obj = new GlobalMessageHandler( $this->userService,$this->messageService);
        return response()->json(class_exists(GlobalMessageHandler::class));
    }
}
