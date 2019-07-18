<?php


namespace App\ChatServices\WebSocketServices;

use App\{User,Message};
use Ratchet\ConnectionInterface;


class UserService
{

    public function __construct(){

    }
    function sayHello(){
        return json_encode(['hello'=>' Im` message from server']);
    }

    function getUserById($id){
        return User::find($id);
    }

    function getUserByConnection($conn){
        $token = $conn->httpRequest->getUri()->getQuery();
        $Users=User::where('token',$token);
        if($Users->count()==1){
            return $Users->first();
        }
        return false;


    }



    function isAdmin(int $id){
        $probablyAdmin=User::find($id);
        if($probablyAdmin->isAdmin===1){
            return true;
        }
        return false;

    }
    function getAllUsersArray(){
        $res=[];
        $Users=User::all();
        foreach($Users as $user){
            $res[]=[
                'id'=>$user->id,
                'username'=>$user->login,
                'banned'=>$user->banned,
                'muted'=>$user->muted
            ];
        }
        return $res;
    }

    function changeBanned($id){
        $targetUser=User::find($id);
        $was=$targetUser->banned;
        $targetUser->banned=!($was);
        $targetUser->save();

    }
    function changeMuted($id){
        $targetUser=User::find($id);
        $was=$targetUser->muted;
        $targetUser->muted=!($was);
        $targetUser->save();
    }
    function filterAdmins(Array $connections){
        return array_filter($connections,function( $el){return $el->isAdmin;});
    }




}