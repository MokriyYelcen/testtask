<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\User;

class LoginController extends Controller
{
    public function index(){
        return 'LoginController';
    }

    public function login(LoginRequest $request){

        $users=User::where('login',$request['login']);
        if($users->count()==1){
            $user=$users->firstOrFail();
            if($user->password==$request['password']){
                $newToken=Str::random(32);
                $user->token=$newToken;
                $user->save();
                return response()->json(['token' => $newToken]);
            }
            else{
                return response()->json(['token' => null]);
            }

        }
        elseif($users->count()==0){
            $newUser= new User;
            $newUser->login=$request['login'];
            $newUser->password=$request['password'];
            $newToken=Str::random(32);
            $newUser->token=$newToken;
            $newUser->save();
            return response()->json(['token' => $newToken]);

        }

        return 'LoginController@login';

    }
}
