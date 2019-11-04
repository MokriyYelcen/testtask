<?php


namespace App\ChatServices\WebSocketServices;


use App\Http\Controllers\WebSocketController;

class MessageRouter
{
    protected $messageTypes=[];
    protected $WebSocketController ;
    public function __construct(WebSocketController $WebSocketController)
    {
        $this->WebSocketController = $WebSocketController;
    }

    public function sayHello(){
        dd("oh shit, that`s great, i`m working through the dependency injection");
    }
    public function type($typeName,$action){
        if(gettype($action) =='string' ||is_callable($action)){
            $this->messageTypes[$typeName][] = $action;
        }else{
            dd('wrong type of attached action');
        }
    }
    public function message($conn,$message){
        try{
            $message = json_decode($message);
            if(array_key_exists($message->type,$this->messageTypes)){
                foreach($this->messageTypes[$message->type] as $action){
                    if(is_callable($action)){
                        $action($conn,$message,$this->WebSocketController);
                    }else if(is_string($action)){
                        $actionArr = explode('@',trim($action));
                        $controllerName = $actionArr[0];
                        $actionName = $actionArr[1];
                        $controllerObj = new $controllerName();
                        $controllerObj->{$actionName}($conn,$message,$this->WebSocketController);
                    }
                }
            }
        }catch(\Exception $e){
            throw $e;
        }
    }

}
