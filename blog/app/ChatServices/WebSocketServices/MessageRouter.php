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
        $message = json_decode($message);
        if(array_key_exists($message->type,$this->messageTypes)){
            foreach($this->messageTypes[$message->type] as $action){
                switch($this->getActionTypeString($action)){
                    case'callable':
                        $action($conn,$message,$this->WebSocketController);
                        break;
                    case'action':
                        $actionArr = explode('@',trim($action));
                        $controllerName = 'App\\ChatServices\\MessageHandlers\\'.$actionArr[0];
                        $actionName = $actionArr[1];
                        $controllerObj = new $controllerName(...($this->getConstructorArguments($controllerName)));
                        $controllerObj->{$actionName}($conn,$message,$this->WebSocketController);
                        break;
                    case'handler':
                        $controllerName = 'App\\ChatServices\\MessageHandlers\\'.$action;
                        $controllerObj = new $controllerName(...($this->getConstructorArguments($controllerName)));
                        $controllerObj->handle($conn,$message,$this->WebSocketController);
                        break;
                }
            }
        }
    }
    protected function getActionTypeString($action){
        if(is_callable($action))return'callable';
        if(gettype($action) == 'string') {
            $actionFormat = substr_count($action,'@');
            if($actionFormat == 1)return'action';
            if($actionFormat == 0){
                if(class_exists('App\\ChatServices\\MessageHandlers\\'.$action)){
                    return'handler';
                }else{
                    throw new \Exception('Class '.$action.' has not been declared yet.');
                }
            }
        }
        throw new \Exception(' Unacceptable action format " '.$action.' "');
    }
    protected function getConstructorArguments($className){
        $reflector = new \ReflectionClass($className);
        $constructor = $reflector->getConstructor();
        $outParams =[];

        if ($constructorParams = $constructor->getParameters()) {
            foreach ($constructorParams as $i => $param) {
                $name = $param->getClass() ? $param->getClass()->name : $param->name;
                $outParams[]=resolve($name);
            }
        }
        return $outParams;
    }

}
