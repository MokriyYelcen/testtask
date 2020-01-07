<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\{User,color};

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
                if(!$user->banned){
                    $newToken=Str::random(32);
                    $user->token=$newToken;
                    $newColorId=color::inRandomOrder()->first()->id;    //that test commit SM-3
                    $user->color_id=$newColorId;
                    $user->save();
                    return response()->json(['token' => $newToken]);
                }
                else{
                    return response('{"wrong":"you are banned"}',401);
                }

            }
            else{
                return response('{"wrong":"wrong password"}',401);
            }

        }
        elseif($users->count()==0){
            $newUser= new User;
            $newUser->login=$request['login'];
            $newUser->password=$request['password'];
            $newToken=Str::random(32);
            $newUser->token=$newToken;
            $newColorId=color::inRandomOrder()->first()->id;
            $newUser->color_id=$newColorId;
            $newUser->save();
            return response()->json(['token' => $newToken]);

        }

        return 'LoginController@login';

    }
}
