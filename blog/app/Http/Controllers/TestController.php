<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Facades\MessageRouterFacade as Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\{ User, color};

class TestController extends Controller
{
    public function test(Request $request){
        try{
//            $login = Message::find(1)->user->login;

            //Message::sayHello();
//            return response()->json(color::inRandomOrder()->first()->id);
            return response()->json(Config::get('webSocket.port'));
        }catch(Exception $e){
        var_dump($e->getFile());
        var_dump($e->getRow());
        var_dump($e->getMessage());
        }

    }
}
