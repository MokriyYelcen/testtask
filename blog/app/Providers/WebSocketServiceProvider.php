<?php

namespace App\Providers;

use App\ChatServices\WebSocketServices\{MessageRouter,MessageService,UserService};
use App\Http\Controllers\WebSocketController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class WebSocketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WebSocketController::class ,function(){
            return new WebSocketController();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
//        $server = IoServer::factory(
//            new HttpServer(
//                new WsServer(
//                    $wbController
//                )
//            ),
//            8090
//        );
//        $server->run();
        $this->app->singleton(MessageRouter::class ,function($app){return new MessageRouter($app->make(WebSocketController::class));});
        if(file_exists(Config::get('webSocket.messageRoutesPath'))){
            require_once (Config::get('webSocket.messageRoutesPath'));
        }

        $this->app->bind(MessageService::class,function($app){
            return new MessageService($app->make(WebSocketController::class));
        });
        $this->app->bind(UserService::class,function($app){
            return new UserService();
        });
    }
}
