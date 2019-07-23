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

    function getUserByConnection(ConnectionInterface $conn){
//        $token = $conn->httpRequest->getUri()->getQuery();
        $Users = User::where(
            'token',
            $conn->httpRequest->getUri()->getQuery()
        );

//        if($Users->count()==1){
//            return $Users->first();
//        }

        return $Users->count() === 1 ? $Users->first() : false;
    }

    function isAdmin(int $id){
        $probablyAdmin = User::find($id);

//        if($probablyAdmin->isAdmin===1){
//            return true;
//        }

        return ($probablyAdmin->isAdmin===1);

    }
    function getAllUsersArray(){
//        $res=[];
//        $Users=User::all();

        return User::all()->map(function($user){
            return [
                'id'=>$user->id,
                'username'=>$user->login,
                'banned'=>$user->banned,
                'muted'=>$user->muted
            ];
        });

//        foreach($Users as $user){
//            $res[]=[
//                'id'=>$user->id,
//                'username'=>$user->login,
//                'banned'=>$user->banned,
//                'muted'=>$user->muted
//            ];
//        }
//
//        return $res;
    }

    function changeBanned($id){
        /** @var User $targetUser */
        if ($targetUser = User::find($id)){
            $was = $targetUser->banned;
            $targetUser->banned = !($was);

            return $targetUser->save();
        }

        return false;
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